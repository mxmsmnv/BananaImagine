# Banana Imagine

**Version:** 1.1.0  
**Repository:** [github.com/mxmsmnv/BananaImagine](https://github.com/mxmsmnv/BananaImagine)  
**Author:** Maxim Alex  
**License:** MIT

Banana Imagine is a ProcessWire module that enables high-quality AI image generation within your `Pageimage` fields using the **Google Gemini** API.

## Features

- **Integrated UI**: Seamless generation bar below your image fields.
- **Batch Generation**: Generate up to 4 variations at once.
- **Smart Variations**: Automatically adds subtle descriptors to batch prompts for variety.
- **Native Storage**: Selected images are saved directly to the page using ProcessWire's native methods.
- **Clean Naming**: Files are saved as `[PageID]-[Timestamp].jpg`.
- **System Prompt**: Define a reusable base prompt in module settings, pre-filled into the input field on every page. Supports `%fieldname%` placeholders (e.g. `%title%`) that are automatically resolved from the current page's field values.

## Installation

1. Upload the `BananaImagine` folder to your `/site/modules/` directory.
2. Go to **Modules > Refresh**.
3. Install **Banana Imagine**.

## Configuration

1. Obtain an API Key from [Google AI Studio](https://aistudio.google.com/).
2. Enter the key in the module settings.
3. Optionally set a **System Prompt** — a base context pre-filled into the prompt field on every page. Use `%fieldname%` placeholders to inject page field values (e.g. `Professional photo of %title%, white background`).
4. Select which image fields should display the Banana Imagine bar.
5. **Note**: Google requires a linked billing account to use image-generation models.

## How to Use

1. Edit a page that has an enabled image field.
2. Locate the yellow **Banana Imagine** bar.
3. The prompt field will be pre-filled with the system prompt (if configured). Edit or extend it as needed.
4. Choose the number of variations (1-4) and click **Generate**.
5. Images will appear as they are processed.
6. Click on the images you want to save. A yellow checkmark will appear on selected items.
7. **Save the Page**. The selected images will be downloaded and added to your field permanently.
