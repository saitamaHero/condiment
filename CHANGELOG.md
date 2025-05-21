# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/)
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]
### Added



### Changed
- Updated the `addConditions` method in the `Evaluator` class to support a new, clearer format. This change improves readability and makes it easier to understand what's being added, replacing the previous argument-order-based approach that the method `addCondition` was expecting.

### Fixed

## [v0.2.0] - 2025-05-16
### Fixed
- Added more tests for testing evaluator 
- Added new classes for group conditions by operators (AND, OR) when replace the default grouping with a single operator grouping.
- Added argument resolving from datasource (array) from Evaluator class using `@@[key]` using dot notation to access nested properties e.g. `@@my.nested.property`
- Removed to string representation for operators to find a cleaner way to do it

## [v0.1.2] - 2025-03-25
### Fixed
- Changed grouping conditions outside evaluator class to decouple complexity

## [v0.1.1] - 2025-03-20
### Fixed
- Removed the display conditions output from evaluator class.

## [v0.1.0] - 2025-03-18
### Added
- Initial release of the library
