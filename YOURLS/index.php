<?php include 'frontend/header.php'; ?>

<body>

<?php
	// Start YOURLS engine
	require_once( dirname(__FILE__).'/includes/load-yourls.php' );

	// URL of the public interface
	$page = YOURLS_SITE . '/index.php' ;

	// Make variables visible to function & UI
	$shorturl = $message = $title = $status = '';

	// Part to be executed if FORM has been submitted
	if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ) {
		if (enableRecaptcha) {
			// Use reCAPTCHA
			$token = $_POST['token'];
			$action = $_POST['action'];
			
			// call curl to POST request
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => recaptchaV3SecretKey, 'response' => $token)));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			$arrResponse = json_decode($response, true);
			
			// verify the response
			if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
				// reCAPTCHA succeeded
				shorten();
			} else {
				// reCAPTCHA failed
				$message = "reCAPTCHA failed";
			}
		} else {
			// Don't use reCAPTCHA
			shorten();
		}
	}

	function shorten() {
		// Get parameters -- they will all be sanitized in yourls_add_new_link()
		$url     = $_REQUEST['url'];
		$keyword = isset( $_REQUEST['keyword'] ) ? $_REQUEST['keyword'] : '' ;
		$title   = isset( $_REQUEST['title'] ) ?  $_REQUEST['title'] : '' ;
		$text    = isset( $_REQUEST['text'] ) ?  $_REQUEST['text'] : '' ;

		// Create short URL, receive array $return with various information
		$return  = yourls_add_new_link( $url, $keyword, $title );
		
		// Make visible to UI
		global $shorturl, $message, $status, $title;

		$shorturl = isset( $return['shorturl'] ) ? $return['shorturl'] : '';
		$message  = isset( $return['message'] ) ? $return['message'] : '';
		$title    = isset( $return['title'] ) ? $return['title'] : '';
		$status   = isset( $return['status'] ) ? $return['status'] : '';
		
		// Stop here if bookmarklet with a JSON callback function ("instant" bookmarklets)
		if( isset( $_GET['jsonp'] ) && $_GET['jsonp'] == 'yourls' ) {
			$short = $return['shorturl'] ? $return['shorturl'] : '';
			$message = "Short URL (Ctrl+C to copy)";
			header('Content-type: application/json');
			echo yourls_apply_filter( 'bookmarklet_jsonp', "yourls_callback({'short_url':'$short','message':'$message'});" );
			die();
		}
	}
?>

	<div class="container-fluid h-100">
		<div class="row justify-content-center align-items-center h-100">
			<div class="col-12 col-lg-10 col-xl-8 col-xxl-5 mt-5">
				<div class="card border-0 mt-5">
					<?php if( isset($status) && $status == 'success' ):  ?>
						<?php $url = preg_replace("(^https?://)", "", $shorturl );  ?>

						<div class="close-container text-end mt-3 me-3">
							<button type="button" class="btn-close" id="close-shortened-screen" aria-label="Close"></button>
						</div>

						<div class="card-body px-5 pb-5">
							<h2 class="text-uppercase text-center">Your shortened link</h2>
							
							<div class="row justify-content-center">
								<div class="col-10">
									<div class="input-group input-group-block mt-4 mb-3">
										<input type="text" class="form-control text-uppercase" value="<?php echo $shorturl; ?>" required>
										<button class="btn btn-primary text-uppercase py-2 px-5 mt-2 mt-md-0" type="submit" id="copy-button" data-shorturl="<?php echo $shorturl; ?>">Copy</button>
									</div>
									<span class="info">View info &amp; stats at <a href="<?php echo $shorturl; ?>+"><?php echo $url; ?>+</a></span>
								</div>
							</div>
						</div>
					<?php else: ?>
						<div class="text-center">
							<img src="<?php echo YOURLS_SITE ?><?php echo "/zi_xiugai/jntm.png" ?>" alt="Logo" width="95px" class="mt-n5">
						</div>
						<div class="card-body px-md-5">
							<?php if ( isset( $_REQUEST['url'] ) && $_REQUEST['url'] != 'http://' ): ?>
								<?php if (strpos($message,'added') === false): ?>
									<div class="alert alert-danger alert-dismissible fade show" role="alert">
										<span>Oh no, <?php echo $message; ?>!</span>
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>	    
								<?php endif; ?>
							<?php endif; ?>

							<form id="shortenlink" method="post" action="">
								<div class="input-group input-group-block mt-4 mb-3">
									<input type="url" name="url" id="url" class="form-control text-uppercase" placeholder="Paste Your URL" aria-label="PASTE URL, SHORTEN &amp; SHARE" aria-describedby="shorten-button" required>
									<input class="btn btn-primary text-uppercase py-2 px-4 mt-2 mt-md-0" type="submit" id="shorten-button" value="确定" />
								</div>
								<?php if (enableCustomURL): ?>
									<a class="btn btn-sm btn-white text-black-50 text-uppercase" data-bs-toggle="collapse" href="#customise-link" role="button" aria-expanded="false" aria-controls="customise-link">
										<img src="<?php echo YOURLS_SITE ?>/frontend/assets/svg/custom-url.svg" alt="Options"></a>
									<div class="collapse" id="customise-link">
										<div class="mt-2 card card-body">
											<div class="d-flex  align-items-center">
												<span class="me-2"><?php echo preg_replace("(^https?://)", "", YOURLS_SITE ); ?>/</span>
												<input type="text" name="keyword" class="form-control form-control-sm text-uppercase" placeholder="Custom URL" aria-label="CUSTOM URL">
											</div>
										</div>
									</div>
								<?php endif; ?>
							</form>
						</div>
					<?php endif; ?>
				</div>
				<div class="d-flex flex-column flex-md-row align-items-center my-3">
					<span class="text-white fw-light"></span>
					<!--<div class="ms-3">
						<?php foreach ($footerLinks as $key => $val): ?>
							<a class="bold-link me-3 text-white text-decoration-none" href="<?php echo $val ?>"><span><?php echo $key ?></span></a>
						<?php endforeach ?>
					</div>-->
				</div>
			</div>
		</div>
	</div>
	<?php include 'frontend/footer.php'; ?>
<!--渐变背景-->
<canvas id="canvas-basic"></canvas>
<script src="https://npm.elemecdn.com/granim@2.0.0/dist/granim.min.js"></script>
<script>
var granimInstance = new Granim({
    element: '#canvas-basic',
    direction: 'left-right',
    isPausedWhenNotInView: true,
    states : {
        "default-state": {
            gradients: [
                ['#a18cd1', '#fbc2eb'],
                 ['#fff1eb', '#ace0f9'],
                 ['#d4fc79', '#96e6a1'],
                 ['#a1c4fd', '#c2e9fb'],
                 ['#a8edea', '#fed6e3'],
                 ['#9890e3', '#b1f4cf'],
                 ['#a1c4fd', '#c2e9fb'],
                 ['#fff1eb', '#ace0f9']
           
            ]
        }
    }
});
<!--下框去边框-->
document.getElementsByClassName("mt-2 card card-body")[0].style="border:0px"
</script>
<!--一言诗词替换默认示例网址-->
<script src="https://sdk.jinrishici.com/v2/browser/jinrishici.js" charset="utf-8"></script>
<script type="text/javascript">
  jinrishici.load(function(result) {
    var sentence = document.querySelector("#url")
var dynasty = result.data.origin.dynasty
var wanzheng = result.data.content + '——' + result.data.origin.author + '《' + result.data.origin.title + '》'
sentence.setAttribute('placeholder',wanzheng)});
</script>          

<!-- 网页鼠标点击特效 - 核心价值观关键字 -->
<script>
    (function () {
        var a_idx = 0;
        window.onclick = function (event) {
            var a = new Array("❤富强❤", "❤民主❤", "❤文明❤", "❤和谐❤", "❤自由❤", "❤平等❤", "❤公正❤", "❤法治❤", "❤爱国❤",
                "❤敬业❤", "❤诚信❤", "❤友善❤");
            var heart = document.createElement("b"); //创建b元素
            heart.onselectstart = new Function('event.returnValue=false'); //防止拖动

            document.body.appendChild(heart).innerHTML = a[a_idx]; //将b元素添加到页面上
            a_idx = (a_idx + 1) % a.length;
            heart.style.cssText = "position: fixed;left:-100%;"; //给p元素设置样式

            var f = 13, // 字体大小
                x = event.clientX - f / 2 - 30, // 横坐标
                y = event.clientY - f, // 纵坐标
                c = randomColor(), // 随机颜色
                a = 1, // 透明度
                s = 0.8; // 放大缩小

            var timer = setInterval(function () { //添加定时器
                if (a <= 0) {
                    document.body.removeChild(heart);
                    clearInterval(timer);
                } else {
                    heart.style.cssText = "font-size:16px;cursor: default;position: fixed;color:" +
                        c + ";left:" + x + "px;top:" + y + "px;opacity:" + a + ";transform:scale(" +
                        s + ");";

                    y--;
                    a -= 0.016;
                    s += 0.002;
                }
            }, 15)
        }
        // 随机颜色
        function randomColor() {
            return "rgb(" + (~~(Math.random() * 255)) + "," + (~~(Math.random() * 255)) + "," + (~~(Math
                .random() * 255)) + ")";
        }
    }());
</script>

</body>
</html>
