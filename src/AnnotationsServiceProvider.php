<?php

namespace Collective\Annotations;

use Collective\Annotations\Console\EventScanCommand;
use Collective\Annotations\Console\ModelScanCommand;
use Collective\Annotations\Console\RouteScanCommand;
use Collective\Annotations\Database\Eloquent\Attributes\AttributeStrategy as ModelScanAttributeStrategy;
use Collective\Annotations\Database\Scanner as ModelScanner;
use Collective\Annotations\Database\ScanStrategyInterface as ModelScanStrategy;
use Collective\Annotations\Events\Attributes\AttributeStrategy as EventsScanAttributeStrategy;
use Collective\Annotations\Events\Scanner as EventScanner;
use Collective\Annotations\Events\ScanStrategyInterface as EventsScanStrategy;
use Collective\Annotations\Filesystem\ClassFinder;
use Collective\Annotations\Routing\Attributes\AttributeStrategy as RouteScanAttributeStrategy;
use Collective\Annotations\Routing\Scanner as RouteScanner;
use Collective\Annotations\Routing\ScanStrategyInterface as RouteScanStrategy;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AnnotationsServiceProvider extends ServiceProvider
{
    use DetectsApplicationNamespace;

    /**
     * The commands to be registered.
     *
     * @var array<string, string>
     */
    protected $commands = [
      'EventScan' => 'command.event.scan',
      'RouteScan' => 'command.route.scan',
      'ModelScan' => 'command.model.scan',
    ];

    /**
     * The classes to scan for event annotations.
     *
     * @var list<string>
     */
    protected $scanEvents = [];

    /**
     * The classes to scan for route annotations.
     *
     * @var list<string>
     */
    protected $scanRoutes = [];

    /**
     * The classes to scan for model binding annotations.
     *
     * @var list<string>
     */
    protected $scanModels = [];

    /**
     * The namespace to scan for models in.
     *
     * @var ?string
     */
    protected $scanModelsInNamespace = null;

    /**
     * Determines if we will auto-scan in the local environment.
     *
     * @var bool
     */
    protected $scanWhenLocal = false;

    /**
     * Determines whether or not to automatically scan the controllers
     * directory (App\Http\Controllers) for routes.
     *
     * @var bool
     */
    protected $scanControllers = false;

    /**
     * Determines whether or not to automatically scan all namespaced
     * classes for event, route, and model annotations.
     *
     * @var bool
     */
    protected $scanEverything = false;

    /**
     * File finder for annotations.
     *
     * @var AnnotationFinder
     */
    protected $finder;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->finder = new AnnotationFinder($app);
        parent::__construct($app);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->determineStrategy();

        $this->registerRouteScanner();
        $this->registerEventScanner();
        $this->registerModelScanner();

        $this->registerCommands();
    }

    /**
     * Register the application's annotated event listeners.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadAnnotatedModels();
        $this->loadAnnotatedEvents();
        if (!$this->app->routesAreCached()) {
            $this->loadAnnotatedRoutes();
        }
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        foreach (array_keys($this->commands) as $command) {
            $method = "register{$command}Command";
            call_user_func_array([$this, $method], []);
        }
        $this->commands(array_values($this->commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerEventScanCommand()
    {
        $this->app->singleton('command.event.scan', function (Application $app) {
            return new EventScanCommand($app['files'], $this);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerRouteScanCommand()
    {
        $this->app->singleton('command.route.scan', function (Application $app) {
            return new RouteScanCommand($app['files'], $this);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerModelScanCommand()
    {
        $this->app->singleton('command.model.scan', function (Application $app) {
            return new ModelScanCommand($app['files'], $this);
        });
    }

    /**
     * @return void
     */
    protected function determineStrategy()
    {
        $this->app->bind(ModelScanStrategy::class, ModelScanAttributeStrategy::class);
        $this->app->bind(EventsScanStrategy::class, EventsScanAttributeStrategy::class);
        $this->app->bind(RouteScanStrategy::class, RouteScanAttributeStrategy::class);
    }

    /**
     * Register the scanner.
     *
     * @return void
     */
    protected function registerRouteScanner()
    {
        $this->app->singleton('annotations.route.scanner', RouteScanner::class);
    }

    /**
     * Register the scanner.
     *
     * @return void
     */
    protected function registerEventScanner()
    {
        $this->app->singleton('annotations.event.scanner', EventScanner::class);
    }

    /**
     * Register the scanner.
     *
     * @return void
     */
    protected function registerModelScanner()
    {
        $this->app->singleton('annotations.model.scanner', ModelScanner::class);
    }

    /**
     * Load the annotated events.
     *
     * @return void
     */
    public function loadAnnotatedEvents()
    {
        if ($this->app->environment('local') && $this->scanWhenLocal) {
            $this->scanEvents();
        }

        $scans = $this->eventScans();

        if (!empty($scans) && $this->finder->eventsAreScanned()) {
            $this->loadScannedEvents();
        }
    }

    /**
     * Scan the events for the application.
     *
     * @return void
     */
    protected function scanEvents()
    {
        $scans = $this->eventScans();

        if (empty($scans)) {
            return;
        }

        /** @var EventScanner $scanner */
        $scanner = $this->app->make('annotations.event.scanner');

        $scanner->setClassesToScan($scans);

        $this->saveScannedCache($this->finder->getScannedEventsPath(), $scanner->getEventDefinitions());
    }

    /**
     * Load the scanned events for the application.
     *
     * @return void
     */
    protected function loadScannedEvents()
    {
        require $this->finder->getScannedEventsPath();
    }

    /**
     * Load the annotated routes.
     *
     * @return void
     */
    protected function loadAnnotatedRoutes()
    {
        if ($this->app->environment('local') && $this->scanWhenLocal) {
            $this->scanRoutes();
        }

        $scans = $this->routeScans();

        if (!empty($scans) && $this->finder->routesAreScanned()) {
            $this->loadScannedRoutes();
        }
    }

    /**
     * Scan the routes and write the scanned routes file.
     *
     * @return void
     */
    protected function scanRoutes()
    {
        $scans = $this->routeScans();

        if (empty($scans)) {
            return;
        }

        /** @var RouteScanner $scanner */
        $scanner = $this->app->make('annotations.route.scanner');

        $scanner->setClassesToScan($scans);

        $this->saveScannedCache($this->finder->getScannedRoutesPath(), $scanner->getRouteDefinitions());
    }

    /**
     * Load the scanned application routes.
     *
     * @return void
     */
    protected function loadScannedRoutes()
    {
        $this->app->booted(function () {
            $router = $this->app['Illuminate\Contracts\Routing\Registrar'];

            require $this->finder->getScannedRoutesPath();
        });
    }

    /**
     * Load the annotated models.
     *
     * @return void
     */
    protected function loadAnnotatedModels()
    {
        if ($this->app->environment('local') && $this->scanWhenLocal) {
            $this->scanModels();
        }

        $scans = $this->modelScans();

        if (!empty($scans) && $this->finder->modelsAreScanned()) {
            $this->loadScannedModels();
        }
    }

    /**
     * Scan the models and write the scanned models file.
     *
     * @return void
     */
    protected function scanModels()
    {
        $scans = $this->modelScans();

        if (empty($scans)) {
            return;
        }

        /** @var ModelScanner $scanner */
        $scanner = $this->app->make('annotations.model.scanner');

        $scanner->setClassesToScan($scans);

        $this->saveScannedCache($this->finder->getScannedModelsPath(), $scanner->getModelDefinitions());
    }

    /**
     * Load the scanned application models.
     *
     * @return void
     */
    protected function loadScannedModels()
    {
        $this->app->booted(function () {
            $router = $this->app['Illuminate\Contracts\Routing\Registrar'];

            require $this->finder->getScannedModelsPath();
        });
    }

    /**
     * Get the classes to be scanned by the provider.
     *
     * @return list<string>
     */
    public function eventScans()
    {
        if ($this->scanEverything) {
            return $this->getAllClasses();
        }

        return $this->scanEvents;
    }

    /**
     * Get the classes to be scanned by the provider.
     *
     * @return list<string>
     */
    public function routeScans()
    {
        if ($this->scanEverything) {
            return $this->getAllClasses();
        }

        $classes = array_unique($this->scanRoutes);

        // scan the controllers namespace if the flag is set
        if ($this->scanControllers) {
            $classes = array_merge(
              $classes,
              $this->getClassesFromNamespace($this->getAppNamespace().'Http\\Controllers')
            );
        }

        return $classes;
    }

    /**
     * Get the classes to be scanned by the provider.
     *
     * @return list<string>
     */
    public function modelScans()
    {
        if ($this->scanEverything) {
            return $this->getAllClasses();
        }

        if ($this->scanModelsInNamespace) {
            return $this->getClassesFromNamespace($this->scanModelsInNamespace);
        }

        return $this->scanModels;
    }

    /**
     * Convert the given namespace to a file path.
     *
     * @param string $namespace the namespace to convert
     *
     * @return string
     */
    public function convertNamespaceToPath($namespace)
    {
        // remove the app namespace from the namespace if it is there
        $appNamespace = $this->getAppNamespace();

        if (substr($namespace, 0, strlen($appNamespace)) == $appNamespace) {
            $namespace = substr($namespace, strlen($appNamespace));
        }

        // trim and return the path
        return str_replace('\\', '/', trim($namespace, ' \\'));
    }

    /**
     * Get a list of the classes in a namespace. Leaving the second argument
     * will scan for classes within the project's app directory.
     *
     * @param string $namespace the namespace to search
     * @param ?string $base
     *
     * @return list<string>
     */
    public function getClassesFromNamespace($namespace, $base = null)
    {
        $directory = ($base ?: $this->app->make('path')).'/'.$this->convertNamespaceToPath($namespace);

        /** @var ClassFinder $finder */
        $finder = $this->app->make('Collective\Annotations\Filesystem\ClassFinder');

        return $finder->findClasses($directory);
    }

    /**
     * Get a list of classes in the root namespace.
     *
     * @return list<string>
     */
    protected function getAllClasses()
    {
        return $this->getClassesFromNamespace($this->getAppNamespace());
    }

    /**
     * Save the new events/models/routes cache, IF it has changed.
     */
    protected function saveScannedCache(string $filepath, string $code): void
    {
        $code = '<?php '.PHP_EOL.PHP_EOL.$code.PHP_EOL;
        if (!file_exists($filepath) || file_get_contents($filepath) != $code) {
            file_put_contents($filepath, $code);
        }
    }
}
