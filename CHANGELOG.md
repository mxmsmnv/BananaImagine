# Changelog

## [1.1.1] - 2026-07-18
### Added
- Gemini 3.1 Flash Image (Nano Banana 2) and Gemini 3.1 Flash Lite Image (Nano Banana 2 Lite) model options.
- Olivia-compatible `AGENTS.md` with site-building guidance, request contracts and safety boundaries.
- New hand-drawn editorial illustration for the project README.

### Changed
- Set Gemini 3.1 Flash Image as the default image-generation model.
- Replaced the Gemini 3 Pro Image preview identifier with the stable `gemini-3-pro-image` identifier while preserving compatibility with existing settings.
- Reworked the README using the same concise documentation structure as Vox.

### Fixed
- Display the Banana Imagine interface for enabled image fields inside RepeaterMatrix items.

## [1.1.0] - 2026-03-05
### Added
- System Prompt field in module settings.
- `%fieldname%` placeholder support (e.g. `%title%`) resolved from current page fields.
- System prompt is pre-filled into the prompt input field on every page edit.

## [1.0.0] - 2026-02-23
### Added
- Initial 1.0.0 Release.
- Support for Gemini 2.5 Flash Image and 3 Pro Image models.
- Batch generation with Smart Variation logic.
- Native image processing with custom naming `[ID]-[Timestamp]`.
- English localization for all UI elements and comments.
- Interactive selection UI with yellow branding.
