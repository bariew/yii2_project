<?php
/**
 * Modal class file
 */


namespace app\modules\common\widgets;

/**
 * Class Modal
 * @package app\modules\common\widgets
 */
class Modal extends \yii\bootstrap4\Modal
{
    public $title = '<div></div>';
    public $options = ['id' => 'ajax-modal'];
    public $size = self::SIZE_LARGE;
    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->view->registerJs(<<<JS

$(document).on('click', '[data-toggle="ajax-modal"]', function(e) {
    e.preventDefault();
    var href = $(this).data('remote') || $(this).attr('href');
    if (href.indexOf('.') > 0) { // renders an image
        $('#ajax-modal').find('.modal-body').html('<div><img style="width:100%" src="'+href+'" />').end().modal();
    } else if ($(this).attr('data-force')) {
        $('#ajax-modal').modal().load(href);
    } else {
        $.post(href, $(this).data('params'), function (data) {
            $('#ajax-modal-label').html($(data).find('.modal-title').text());
            data && $('#ajax-modal').find('.modal-body').html(data).end().modal();
            $('#ajax-modal .modal-body').find('.modal-title').remove();
        })
    }
});
$(document).on("submit", "#ajax-modal form", function(event){ // ajax submit form from modal window
    event.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        type: $(this).attr("method"),
       // dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function (data) {
             $('#ajax-modal').find('.modal-body').html(data).end().modal();
        }
    });
});
$(document).ready(function(event){ // load modal from url ajax-modal parameter
    var href = (new URL(window.location.href)).searchParams.get("ajax-modal");
    if (href) {
        $.get(href, function (data) {
            data && $('#ajax-modal').find('.modal-body').html(data).end().modal();
        })
    }
});
JS
        );
        return parent::run(); // TODO: Change the autogenerated stub
    }
}