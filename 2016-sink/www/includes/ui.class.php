<?php
    class UI {
        private $title             = '';            // <title>
        private $javascript        = array();
        private $css               = array();

        private $tile_containers   = array();

        // constructor
        public function __construct() {
            $this->addCSS('metro.css');
            $this->addCSS('metro_mobile.css', 'screen and (max-height: 500px), screen and (orientation:portrait)');
            $this->addJavaScript('jquery.js');
            $this->addJavaScript('metro.js');
        }

        // generates page header
        public function getHeader() {
            $html = '<!DOCTYPE html>
<html>
    <head>
        <title>' . $this->getTitle() . '</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">';

            // CSS
            foreach ($this->css as $css) {
                $html .= '
        <link rel="stylesheet" type="text/css" href="css/' . $css[0] . '"' . ($css[1] ? ' media="' . $css[1] . '"' : '') . ' />';
            }

            // JavaScript
            foreach ($this->javascript as $js_file) {
                $html .= '
        <script type="text/javascript" src="javascript/' . $js_file . '"></script>';
            }

            // full : widget width: 90
            // small: widget width: 65 (mobile mode)
            // full container: 12 widgets
            // half container: 4  widgets
            $tile_margin       = 5;
            $tile_width_big    = 90; // 1x1 widget size
            $tile_width_small  = 65; // 1x1 widget size (mobile mode)
            $container_margin  = 50;
            $container_padding = 10;
            $tile_total        = 0;
            foreach ($this->tile_containers as $index => $tile_container) {
                if ($tile_container['size'] === 'full') {
                    $tile_total += 12;
                } else {
                    $tile_total += 4;
                }
            }

            $html .= '
        <!-- Computed in PHP based on your settings -->
        <style>
            #widget_scroll_container {
                width: ' . (($tile_total * ($tile_width_big + $tile_margin * 2)) + ($container_margin * (count($this->tile_containers) - 1)) + ($container_padding * 2 * count($this->tile_containers))) . 'px;
            }
            div.widget_container {
                width: ' . 12 * ($tile_width_big + $tile_margin * 2) . 'px;
            }
            div.widget_container.half {
                width: ' . 4 * ($tile_width_big + $tile_margin * 2) . 'px;
            }
            @media screen and (max-height: 680px) {
                #widget_scroll_container {
                    width: ' . (($tile_total * ($tile_width_small + $tile_margin * 2)) + ($container_margin * (count($this->tile_containers) - 1)) + ($container_padding * 2 * count($this->tile_containers))) . 'px;
                }
                div.widget_container {
                    width: ' . 12 * ($tile_width_small + $tile_margin * 2) . 'px;
                }
                div.widget_container.half {
                    width: ' . 4 * ($tile_width_small + $tile_margin * 2) . 'px;
                }
            }
        </style>
    </head>
    <body>';

            return $html;
        }

        // generates page footer
        public function getFooter() {
            $html = '
        <div id="widget_preview">
            <div id="widget_sidebar">
                <div>
                    <div class="cancel"><span>Close</span></div>
                    <div class="refresh"><span>Refresh</span></div>
                    <div class="back"><span>Back</span></div>
                    <div class="next"><span>Next</span></div>
                </div>
            </div>
        </div>
    </body>
</html>';
            return $html;
        }


        // prints page header
        public function printHeader() {
            print $this->getHeader();
        }

        // prints page footer
        public function printFooter() {
            print $this->getFooter();
        }

        // adds a CSS file to <head>
        public function addCSS($css_file, $media_query = null) {
            $this->css[] = array($css_file, $media_query);
        }

        // adds a JS file to <head>
        public function addJavaScript($javascript_file) {
            $this->javascript[] = $javascript_file;
        }

        // set <title>
        public function setTitle($title) {
            $this->title = $title;
        }

        // retutns <title>
        public function getTitle() {
            return $this->title;
        }

        // add widget container
        public function addTileContainer($tile_container) {
            $this->tile_containers[] = $tile_container;
        }

        public function printTiles() {
            print '
        <div id="widget_scroll_container">';

            foreach ($this->tile_containers as $index => $tile_container) {
                print '
            <div class="widget_container ' . $tile_container['size'] . (strlen($tile_container['theme']) ? $tile_container['theme'] : '') . '" data-num="' . $index . '">';

                foreach ($tile_container['tiles'] as $tile_data) {
                    $tile = new Tile();
                    $tile->setName($tile_data['name']);
                    $tile->setSize($tile_data['size']);
                    $tile->setThumbnail($tile_data['thumbnail']);
                    $tile->setContent($tile_data['content']);
                    $tile->setUrl($tile_data['url']);
                    $tile->setTheme($tile_data['theme']);
                    $tile->setLink($tile_data['link']);

                    if (isset($tile_data['colour'])) {
                        $tile->setColour($tile_data['colour']);
                    }

                    $tile->display();
                }

                print '
            </div>';
            }

            print '
        </div>';
        }
    }
?>