<?php
namespace ProcessWire;

/**
 * BananaImagine: AI Image Generation for ProcessWire via Google Gemini
 * * @version 1.0.0
 * @author Maxim Alex
 * @link https://github.com/mxmsmnv/BananaImagine
 */
class BananaImagine extends InputfieldImage implements ConfigurableModule {

    public static function getModuleInfo() {
        return array(
            'title' => 'Banana Imagine',
            'version' => 110,
            'icon' => 'leaf',
            'author' => 'Maxim Alex',
            'summary' => 'Generate AI images directly in your image fields using Google Gemini API.',
            'autoload' => 'template=admin',
            'requires' => 'ProcessWire>=3.0.0'
        );
    }

    public function ready() {
        // Handle AJAX request
        if($this->wire('input')->post('banana_action') === 'generate') {
            $this->handleBananaRequest();
        }
        
        $this->addHookAfter('InputfieldImage::render', $this, 'renderBananaInterface');
        $this->addHookBefore('InputfieldImage::processInput', $this, 'processBananaInput');
        $this->addHookBefore('ProcessPageEdit::execute', $this, 'addAssets');
    }

    protected function addAssets(HookEvent $event) {
        $config = $this->wire('config');
        $url = $config->urls->siteModules . $this->className() . "/";
        $config->scripts->add($url . "BananaImagine.js?v=" . time());
    }

    protected function handleBananaRequest() {
        $apiKey = $this->bananaApiKey;
        $prompt = $this->wire('input')->post->text('prompt');
        $index  = $this->wire('input')->post->int('index');
        $pageId = $this->wire('input')->post->int('page_id');
        $model = $this->bananaModel ?: 'gemini-2.5-flash-image'; 

        if(!$apiKey) {
            header('Content-Type: application/json');
            die(json_encode(['error' => 'API Key missing.']));
        }

        // System prompt is already included in the user's prompt (pre-filled in the input field)

        // Smart Variations: Adds subtle differences for batch results
        $variations = ["", ", cinematic lighting", ", alternative perspective", ", close-up shot"];
        $finalPrompt = $prompt . ($variations[$index % count($variations)]);

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [['parts' => [['text' => $finalPrompt]]]],
            'generationConfig' => [
                'responseModalities' => ['IMAGE']
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        header('Content-Type: application/json');

        if ($httpCode !== 200) {
            $result = json_decode($response, true);
            $error = $result['error']['message'] ?? 'API Error (' . $httpCode . ')';
            die(json_encode(['error' => $error]));
        }

        $result = json_decode($response, true);

        if(isset($result['candidates'][0]['content']['parts'][0]['inlineData'])) {
            $inline = $result['candidates'][0]['content']['parts'][0]['inlineData'];
            echo json_encode([
                'data' => [['url' => 'data:' . $inline['mimeType'] . ';base64,' . $inline['data']]]
            ]);
        } else {
            echo json_encode(['error' => 'No image data returned.']);
        }
        exit;
    }

    protected function renderBananaInterface(HookEvent $event) {
        $inputfield = $event->object;
        $useFields = is_array($this->useField) ? $this->useField : [];
        if(!in_array($inputfield->name, $useFields)) return;

        $page = $inputfield->hasPage;
        $pageId = $page ? $page->id : 0;

        // Resolve system prompt placeholders to pre-fill the input field
        $systemPrompt = trim($this->systemPrompt ?? '');
        $prefillValue = '';
        if($systemPrompt) {
            $resolved = $systemPrompt;
            if($page && $page->id) {
                $resolved = preg_replace_callback('/%([a-zA-Z0-9_]+)%/', function($matches) use ($page) {
                    $fieldName = $matches[1];
                    $value = $page->get($fieldName);
                    if($value instanceof WireArray) return (string) $value->first();
                    return $value ? (string) $value : $matches[0];
                }, $systemPrompt);
            }
            $prefillValue = htmlspecialchars($resolved, ENT_QUOTES);
        }

        $markup = "
        <div class='BananaImagine-container' data-name='{$inputfield->name}' data-page-id='{$pageId}' style='margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;'>
            <div class='uk-grid-collapse uk-grid' uk-grid>
                <div class='uk-width-expand@s'>
                    <input type='text' class='banana-prompt uk-input' value='{$prefillValue}' placeholder='Describe the image...' style='border-radius: 4px 0 0 4px;'>
                </div>
                <div class='uk-width-auto@s'>
                    <select class='banana-num uk-select' style='min-width: 65px; border-radius: 0; border-left: 0; border-right: 0;'>
                        <option value='1'>1</option>
                        <option value='2'>2</option>
                        <option value='3'>3</option>
                        <option value='4'>4</option>
                    </select>
                </div>
                <div class='uk-width-auto@s'>
                    <button type='button' class='banana-btn-gen ui-button ui-widget ui-state-default' style='background: #f1c40f; color: #000; border: none; border-radius: 0 4px 4px 0; height: 100%; padding: 0 20px;'>
                        <span class='ui-button-text'>Generate</span>
                    </button>
                </div>
            </div>
            <div class='banana-results-area uk-grid-small uk-grid uk-child-width-1-2@s uk-child-width-1-4@m' uk-grid style='margin-top: 10px;'></div>
        </div>";

        $event->return .= $markup;
    }

    public function processBananaInput(HookEvent $event) {
        $inputfield = $event->object;
        $field_name = $inputfield->name;
        $banana_data = $this->wire('input')->post("banana_urls_{$field_name}");
        if(!$banana_data) return;
        
        $page = $inputfield->hasPage; 
        if(!$page) return;
        $page->of(false);
        $field_value = $page->getUnformatted($field_name);

        foreach ($banana_data as $index => $data_string) {
            list($url, $desc) = explode('*', $data_string);
            
            // FILENAME: ID-Timestamp-Index.jpg
            $pageId = $page->id;
            $newFileName = "{$pageId}-" . time() . "-{$index}.jpg";
            
            $tempPath = $this->wire('config')->paths->cache . 'BananaImagine/' . $newFileName;
            if(!is_dir(dirname($tempPath))) wireMkdir(dirname($tempPath));

            if(strpos($url, 'data:image') === 0) {
                $parts = explode(',', $url);
                file_put_contents($tempPath, base64_decode($parts[1]));
            }

            if(file_exists($tempPath)) {
                $pagefile = new Pageimage($field_value, $tempPath);
                $pagefile->description = $desc;
                $field_value->add($pagefile);
                unlink($tempPath);
            }
        }
    }

    public function getModuleConfigInputfields(array $data) {
        $inputfields = new InputfieldWrapper();

        $f = $this->wire('modules')->get('InputfieldTextarea');
        $f->name = 'systemPrompt';
        $f->label = 'System Prompt';
        $f->description = 'Optional context pre-filled into the prompt field on every page. Use `%fieldname%` placeholders to insert values from the current page (e.g. `%title%`, `%summary%`).';
        $f->notes = 'Example: "Professional product photo of %title%, studio lighting, white background"';
        $f->rows = 3;
        $f->value = $data['systemPrompt'] ?? '';
        $inputfields->add($f);

        $f = $this->wire('modules')->get('InputfieldText');
        $f->name = 'bananaApiKey'; 
        $f->label = 'Google AI API Key';
        $f->description = 'Create your API key at [Google AI Studio](https://aistudio.google.com/). Note: Billing is required for image generation.';
        $f->value = $data['bananaApiKey'] ?? '';
        $inputfields->add($f);

        $f = $this->wire('modules')->get('InputfieldSelect');
        $f->name = 'bananaModel';
        $f->label = 'Model';
        $f->addOptions([
            'gemini-2.5-flash-image' => 'Gemini 2.5 Flash Image',
            'gemini-3-pro-image-preview' => 'Gemini 3 Pro Image (Preview)'
        ]);
        $f->value = $data['bananaModel'] ?? 'gemini-2.5-flash-image';
        $inputfields->add($f);

        $f = $this->wire('modules')->get('InputfieldAsmSelect');
        $f->name = 'useField'; 
        $f->label = 'Enabled Fields';
        foreach($this->wire('fields') as $field) {
            if($field->type instanceof FieldtypeImage) $f->addOption($field->name);
        }
        $f->value = $data['useField'] ?? [];
        $inputfields->add($f);

        return $inputfields;
    }
}