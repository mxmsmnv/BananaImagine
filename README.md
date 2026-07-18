# Banana Imagine

Banana Imagine adds Google Gemini image generation directly to ProcessWire image fields, so editors can create, preview, select and save AI-generated images without leaving the page editor.

![Banana Imagine](assets/banana-imagine-illustration.png)

It is made for editorial sites, catalogs, landing pages, directories, portfolios and content teams that want AI-assisted image creation inside their existing ProcessWire workflow.

**Author:** Maxim Semenov<br>
**Website:** [smnv.org](https://smnv.org)<br>
**Email:** [maxim@smnv.org](mailto:maxim@smnv.org)

If this project helps your work, consider supporting future development: [GitHub Sponsors](https://github.com/sponsors/mxmsmnv) or [smnv.org/sponsor](https://smnv.org/sponsor/).

## What Banana Imagine Does

- Adds a generation bar below selected ProcessWire image fields.
- Generates one to four image variations from a single prompt.
- Adds subtle prompt variations for more diverse batches.
- Supports reusable system prompts with `%fieldname%` placeholders.
- Resolves placeholders from the page being edited, such as `%title%` or `%summary%`.
- Lets editors preview and select generated images before saving.
- Stores selected results as native ProcessWire `Pageimage` items.
- Uses clean filenames based on page id, timestamp and result index.
- Recognizes enabled image fields inside RepeaterMatrix items.

## Supported Models

- Gemini 3.1 Flash Image — Nano Banana 2 and the default option.
- Gemini 3.1 Flash Lite Image — Nano Banana 2 Lite.
- Gemini 3 Pro Image — Nano Banana Pro.
- Gemini 2.5 Flash Image — the original Nano Banana model.

Existing `gemini-3-pro-image-preview` settings are migrated automatically to the stable `gemini-3-pro-image` identifier.

## Editorial Workflow

1. An editor opens a ProcessWire page with an enabled image field.
2. The configured system prompt is pre-filled and page placeholders are resolved.
3. The editor adjusts the prompt and requests one to four variations.
4. Results appear below the field as they are generated.
5. The editor selects the images to keep.
6. Saving the page adds those images to the field permanently.

Unselected previews are not added to the page.

## Website Integration

Banana Imagine is an admin editing tool, not a frontend widget. Generated files become normal ProcessWire images and are rendered with the same template code as uploaded images:

```php
<?php foreach($page->hero_images as $image): ?>
    <figure>
        <img src="<?= $image->url ?>" alt="<?= $sanitizer->entities($image->description) ?>">
    </figure>
<?php endforeach; ?>
```

This keeps frontend architecture independent from the image provider. Templates do not need to call Gemini.

## Installation

1. Copy the `BananaImagine` folder into `/site/modules/`.
2. In ProcessWire Admin, refresh modules.
3. Install `Banana Imagine`.
4. Open the module configuration.

## Configuration

1. Create an API key in [Google AI Studio](https://aistudio.google.com/).
2. Ensure the Google account has billing enabled for image generation.
3. Enter the API key in the module settings.
4. Choose the image-generation model.
5. Optionally define a reusable system prompt.
6. Select the image fields that should display the Banana Imagine interface.

Example system prompt:

```text
Professional editorial photograph for %title%, natural light, clean composition
```

## Agent Documentation

See [AGENTS.md](AGENTS.md) for AI-agent guidance, site-building patterns, ProcessWire hooks, request contracts, safety boundaries and validation steps.

See [CHANGELOG.md](CHANGELOG.md) for release notes.

## Author

Maxim Semenov<br>
[smnv.org](https://smnv.org)<br>
[maxim@smnv.org](mailto:maxim@smnv.org)

## License

MIT
