![CI](https://github.com/lolli42/enetcache/workflows/CI/badge.svg)

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

