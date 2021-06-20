<?php
/** @var \yii\web\View $this */
\app\modules\common\widgets\videochat\VideoChatAsset::register($this);
$url = Yii::$app->params['videochat']['address']; // ws://localhost:8090
$this->registerJs(<<<JS
    wsUrl = '$url';
JS
)
; ?>

<div class="custom-modal d-none" id='recording-options-modal'>
    <div class="custom-modal-content">
        <div class="row text-center">
            <div class="col-md-6 mb-2">
                <span class="record-option" id='record-video'>Record video</span>
            </div>
            <div class="col-md-6 mb-2">
                <span class="record-option" id='record-screen'>Record screen</span>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button class="btn btn-outline-danger" id='closeModal'>Close</button>
            </div>
        </div>
    </div>
</div>


<nav class="navbar bg-info rounded-0 d-print-none">
    <div class="text-white">Video Call</div>

    <div class="pull-right room-comm" hidden>
        <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-video' title="Hide Video">
            <i class="fa fa-video text-white"></i>
        </button>

        <button class="btn btn-sm rounded-0 btn-no-effect" id='toggle-mute' title="Mute">
            <i class="fa fa-microphone-alt text-white"></i>
        </button>

        <button class="btn btn-sm rounded-0 btn-no-effect" id='share-screen' title="Share screen">
            <i class="fa fa-desktop text-white"></i>
        </button>

        <button class="btn btn-sm rounded-0 btn-no-effect" id='record' title="Record">
            <i class="fa fa-dot-circle text-white"></i>
        </button>

        <button class="btn btn-sm text-white pull-right btn-no-effect" id='toggle-chat-pane'>
            <i class="fa fa-comment"></i> <span class="badge badge-danger very-small font-weight-lighter" id='new-chat-notification' hidden>New</span>
        </button>

        <button class="btn btn-sm rounded-0 btn-no-effect text-white">
            <a href="/" class="text-white text-decoration-none"><i class="fa fa-sign-out-alt text-white" title="Leave"></i></a>
        </button>
    </div>
</nav>

<div class="container-fluid" id='room-create' hidden>

    <div class="row mt-2">
        <div class="col-12 text-center">
            <span class="form-text small text-danger" id='err-msg'></span>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="room-name">Room Name</label>
            <input type="text" id='room-name' class="form-control rounded-0" placeholder="Room Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="your-name">Your Name</label>
            <input type="text" id='your-name' class="form-control rounded-0" placeholder="Your Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <button id='create-room' class="btn btn-block rounded-0 btn-info">Create Room</button>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3" id='room-created'></div>
    </div>
</div>

<div class="container-fluid" id='username-set' hidden>
    <div class="row">
        <div class="col-12 h4 mt-5 text-center">Your Name</div>
    </div>

    <div class="row mt-2">
        <div class="col-12 text-center">
            <span class="form-text small text-danger" id='err-msg-username'></span>
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <label for="username">Your Name</label>
            <input type="text" id='username' class="form-control rounded-0" placeholder="Your Name">
        </div>

        <div class="col-12 col-md-4 offset-md-4 mb-3">
            <button id='enter-room' class="btn btn-block rounded-0 btn-info">Enter Room</button>
        </div>
    </div>
</div>



<div class="container-fluid room-comm row" hidden>
    <div class="col-sm-3 local-vid-div">
        <video class="col-12 local-video mirror-mode" id='local' volume='0' autoplay muted></video>
    </div>

    <div class="col-sm-3 chat-col mb-2 bg-info" id='chat-pane'>
        <div class="">
            <div class="text-center">CHAT</div>
        </div>

        <div id='chat-messages'></div>

        <div class="">
            <textarea id='chat-input' class="form-control rounded-0 chat-box border-info" rows='3' placeholder="Type here..."></textarea>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="main col-12" id='main-section'>
            <div class="" id='videos'></div>
        </div>
    </div>
</div>