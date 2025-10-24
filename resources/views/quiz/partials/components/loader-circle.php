<div class="growtype-quiz-loader-wrapper"
     data-default-redirect-url="<?php echo $params['default_redirect_url'] ?>"
     data-redirect-url="<?php echo $params['redirect_url'] ?>"
     data-redirect="<?php echo $params['redirect'] ?>"
     data-duration="<?php echo $params['duration'] ?>"
>
    <div class="growtype-quiz-loader-content">
        <p class="e-content"><?php echo $params['content'] ?></p>
        <svg class="radial-progress" data-countervalue="100" viewBox="0 0 80 80">
            <defs>
                <linearGradient id="quizLoaderGradientStroke" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:white;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:white;stop-opacity:1" />
                </linearGradient>
            </defs>
            <circle class="bar-static" cx="40" cy="40" r="35"></circle>
            <circle class="bar--animated" cx="40" cy="40" r="35" style="stroke-dashoffset: 217.8;"></circle>
            <text class="countervalue start" x="50%" y="57%" transform="matrix(0, 1, -1, 0, 80, 0)">100</text>
        </svg>
        <a href="<?php echo $params['default_redirect_url'] ?>" class="btn btn-primary btn-continue"><?php echo $params['continue_btn_text'] ?></a>
    </div>
</div>
