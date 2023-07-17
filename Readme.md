![tests core v11](https://github.com/lolli42/enetcache/actions/workflows/testscorev11.yml/badge.svg)
![tests core v12](https://github.com/lolli42/enetcache/actions/workflows/testscorev12.yml/badge.svg)

# TYPO3 Extension ``enetcache``

## Features

Provides a rather simple API for frontend extensions to put their rendered
content into cache. The extension synchronizes lifetime and tags with the
general page cache. A backend DataHandler hook takes care of automatic cache
flushing if records are changed affecting those plugin cache entries.

## Installation

The extension can be installed from TYPO3 TER via the extension manager but
the recommended way is using composer doing `composer require lolli/enetcache`,
and / or define a dependency in your consuming extensions.

## Usage documentation

Find API documentation at [docs.typo3.org](https://docs.typo3.org/p/lolli/enetcache/3.1/en-us/)

## Development: Release new version

Example release workflow, basically for my own laziness ;)

```
Build/Scripts/runTests.sh -s composerUpdate -t 11
.Build/bin/tailor set-version 4.1.1
git commit -am "[RELEASE] 4.1.1 Bug fixes and improved core v10 / v11 compatibility"
git tag 4.1.1
git push
git push --tags
```
