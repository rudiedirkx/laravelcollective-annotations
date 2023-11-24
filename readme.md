# This is a `laravelcollective/annotations` drop-in replacement

To use this package instead of `laravelcollective/annotations` as a perfect drop-in replacement, do this in your project:

1. `composer require rdx/laravelcollective-annotations`
2. if your project explicitly required `annotations`: `composer remove laravelcollective/annotations`

This will install `rdx/laravelcollective-annotations` and pretend it IS `laravelcollective/annotations`, and all other packages will believe `laravelcollective/annotations` is installed, because Composer is awesome.
