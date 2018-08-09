<?php
if (!isset($argv[1])) die("please mention file to write to!".PHP_EOL);
        $json = json_decode(file_get_contents(dirname($argv[1]) .'/directory-data.json'), true);
        $files = $json['files'];
        $dirs = $json['dirs'];
// Pagination
$maxFiles = 15;
$totalFiles = count($files);
$filePageCount = $totalFiles > 0?ceil($totalFiles / $maxFiles):1;

for ($currentPage = 0; $currentPage < $filePageCount; $currentPage++) {
ob_start();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo isset($_ENV["PAGE_TITLE"])?$_ENV["PAGE_TITLE"]:"index-html"; ?></title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
    <style>
    body {
        text-align: center;
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                margin: 0;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .top-left {
                position: absolute;
                left: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

    .video-preview {
        display: inline-block;
        position: relative;
        background: #ddd;
        overflow: hidden;
        width: 213.5px;
        height: 120px;
        margin: 10px;
        border-radius: 3px;
        box-shadow: 0 0 6px #bbb;
    }
    </style>
</head>
    <body>
        <div class="flex-center position-ref full-height">
                <div class="top-left links">
                        <?php foreach($dirs as $d)
                        echo '<a href="'. $d[0] .'"'.(basename(dirname($d[0])) == basename(dirname($argv[1]))?' style="color: red;"':'').'>/'. $d[1] .'/</a>';
                        ?>
                </div>
                <div class="top-right links">
                        <?php for($i = 0; $i < $filePageCount; $i++)
                        echo '<a href="'.($i == 0?'index':$i).'.html"'.($i==$currentPage?' style="color: red;"':'').'>- '. ($i+1) .' -</a>';
                        ?>
                </div>

            <div class="content">
                <div class="title m-b-md">
                    <?php echo basename(dirname($argv[1])); ?> 
                </div>
        <?php
        foreach (array_values($files) as $j => $file) {
            if ($j >= $maxFiles) break;
            echo '<a href="'. $file[0] .'" target="_blank" class="video-preview" data-frames="100" data-source="'. $file[1] .'"></a>';
        }
        $files = array_splice($files, $maxFiles);
        ?>
		</div>
	</div>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script>
    (function($) {
        $.fn.videoPreview = function(options) {
            return this.each(function() {
                var elm = $(this);
                var frames = parseFloat(elm.data('frames'));

                var img = $('<img/>', { 'src': elm.data('source') }).hide().css({
                    'position': 'absolute', 'cursor': 'pointer'
                }).appendTo(elm);
                var slider = $('<div/>').hide().css({
                    'width': '2px', 'height': '100%', 'background': '#ddd', 'position': 'absolute',
                    'z-index': '1', 'top': '0', 'opacity': 0.6, 'cursor': 'pointer'
                }).appendTo(elm);

                var width;

                function defaultPos() {
                    img.css('left', -width * frames / 4);
                }

                img.load(function() { // we need to know video's full width
                    img.css('width', this.width / 2);
                    $(this).show();
                    
                    width = this.width / frames;
                    elm.css('width', width);
                    elm.css('height', this.height);
                    defaultPos();
                });
                elm.mousemove(function(e) {
                    var left = e.clientX - elm.position().left; // position inside the wrapper
                    slider.show().css('left', left - 1); // -1 because it's 2px width
                    var leftPx = -Math.floor((left / width) * frames) * width;
                    if (!isNaN(leftPx)) {
                        img.css('left', leftPx);
                    }
                }).mouseout(function(e) {
                    slider.hide();
                    defaultPos();
                });

            });
        };
    })(jQuery);

    $('.video-preview').videoPreview();
    </script>
    </body>
</html>
<?php
file_put_contents(($currentPage == 0?$argv[1]:dirname($argv[1])."/{$currentPage}.html"), ob_get_contents()); // Store buffer in variable

ob_end_clean();
}
