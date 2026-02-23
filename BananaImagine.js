(function($) {
    $(document).ready(function() {
        $(document).on('click', '.banana-btn-gen', function(e) {
            e.preventDefault();
            const $container = $(this).closest('.BananaImagine-container');
            const $btn = $(this);
            const $preview = $container.find('.banana-results-area');
            const prompt = $container.find('.banana-prompt').val();
            const num = parseInt($container.find('.banana-num').val());

            if(!prompt) return;

            $btn.addClass('ui-state-disabled').find('.ui-button-text').text('Processing...');
            $preview.html(''); 

            for(let i=0; i < num; i++) {
                $preview.append(`
                    <div class='banana-slot' id='banana-slot-${i}' style='margin-bottom:12px;'>
                        <div style='background:#f4f4f4; aspect-ratio:1/1; display:flex; align-items:center; justify-content:center; color:#888; font-size:11px; border-radius:4px; border:1px dashed #ccc;'>
                            Peeling...
                        </div>
                    </div>
                `);
            }

            let completed = 0;
            for(let i=0; i < num; i++) {
                $.ajax({
                    url: window.location.href,
                    method: 'POST',
                    data: { banana_action: 'generate', prompt: prompt, index: i },
                    success: function(response) {
                        if(response.data && response.data[0]) {
                            const item = response.data[0];
                            const html = `
                                <div class='banana-card-item' style='cursor:pointer;'>
                                    <div class='uk-panel uk-border-rounded' style='border: 3px solid #eee; position: relative; overflow:hidden;'>
                                        <img src='${item.url}' style='display: block; width: 100%;'>
                                        <input type='hidden' class='banana-hidden-input' data-url='${item.url}'>
                                        <div class='banana-badge' style='display:none; position:absolute; top:8px; right:8px; background:#f1c40f; color:#000; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; font-weight:bold;'>✓</div>
                                    </div>
                                </div>`;
                            $(`#banana-slot-${i}`).html(html);
                        } else {
                            $(`#banana-slot-${i}`).html(`<div style='color:#c00; font-size:10px; padding:10px;'>${response.error || 'Error'}</div>`);
                        }
                    },
                    error: function() {
                        $(`#banana-slot-${i}`).html("<div style='color:#c00; font-size:10px; padding:10px;'>Failed</div>");
                    },
                    complete: function() {
                        completed++;
                        if(completed === num) $btn.removeClass('ui-state-disabled').find('.ui-button-text').text('Generate');
                    }
                });
            }
        });

        $(document).on('click', '.banana-card-item', function() {
            const $card = $(this).find('.uk-panel');
            const $input = $(this).find('.banana-hidden-input');
            const $badge = $(this).find('.banana-badge');
            const fieldName = $(this).closest('.BananaImagine-container').data('name');
            const prompt = $(this).closest('.BananaImagine-container').find('.banana-prompt').val();

            if($input.attr('name')) {
                $input.removeAttr('name');
                $card.css('border-color', '#eee');
                $badge.hide();
            } else {
                $input.attr('name', 'banana_urls_' + fieldName + '[]');
                $input.val($input.data('url') + '*' + prompt);
                $card.css('border-color', '#f1c40f');
                $badge.show();
            }
        });
    });
})(jQuery);