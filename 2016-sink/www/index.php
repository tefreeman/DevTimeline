<?php
	require_once('header.php');
    require_once('includes/tile.class.php');
    require_once('includes/ui.class.php');
	require_once('header.html');
    $ui = new UI();

    $ui->setTitle('MelonHTML5 - Metro Framework');

    $tiles = array(
        array(
            'name'        => 'guide',
            'thumbnail'   => 'images/guide.png',
            'content'     => 'Guide',
            'url'         => 'tiles/about.php',
            'size'        => '4x2',
            'theme'       => 'basicblue',
            'link'        => ''
        ),
        array(
            'name'        => 'sinkList',
            'thumbnail'   => 'images/house.png',
            'content'     => 'Find a Handyman',
            'url'         => 'tiles/list.html',
            'size'        => '4x2',
            'theme'       => 'basicblue1',
            'link'        => ''
        ),
        array(
            'name'        => 'money',
            'thumbnail'   => 'images/money.png',
            'content'     => 'Payment Settings',
            'url'         => 'tiles/money.php',
            'size'        => '4x2',
            'theme'       => 'moneygreen',
            'link'        => ''
        ),
	
          array(
            'name'        => 'accountsettings',
            'thumbnail'   => 'images/reviews.png',
            'content'     => 'Account Settings',
            'url'         => 'tiles/accountsettings.php',
            'size'        => '4x2',
            'theme'       => 'basicblue1',
            'link'        => ''
        ),
		array(
            'name'        => 'currentOrders',
            'thumbnail'   => 'images/currentJobs.png',
            'content'     => 'Current Jobs placed',
            'url'         => 'tiles/currentOrders.php',
            'size'        => '4x2',
            'theme'       => 'basicblue',
            'link'        => ''
        ),
	
		array(
            'name'        => 'support',
            'thumbnail'   => 'images/widget_tooltip.png',
            'content'     => 'support',
            'url'         => 'tiles/support.php',
            'size'        => '4x2',
            'theme'       => 'supportgold',
            'link'        => ''
        ),
        array(
            'name'        => 'about',
            'thumbnail'   => 'images/about.png',
            'content'     => 'about',
            'url'         => '/home/',
            'size'        => '2x2',
            'theme'       => 'basicblue2',
            'link'        => '/home/'
        ),
        
        array(
            'name'        => 'map',
            'thumbnail'   => '/images/map.png',
            'content'     => 'Search Location',
            'url'         => 'tiles/map.php',
            'size'        => '2x2',
            'theme'       => 'basicblue',
            'link'        => ''
        ),
        array(
            'name'        => 'yourReviews',
            'thumbnail'   => 'images/reviews.png',
            'content'     => 'Your Reviews',
            'url'         => 'tiles/your-reviews.php',
            'size'        => '2x2',
            'theme'       => 'basicblue1',
            'link'        => ''
        ),
		   array(
            'name'        => 'inviteFriends',
            'thumbnail'   => 'images/invite.png',
            'content'     => 'Invite Friends',
            'url'         => 'tiles/invite.php',
            'size'        => '2x2',
            'theme'       => 'basicblue2',
            'link'        => ''
        ),
        array(
            'name'        => 'tile011',
            'thumbnail'   => 'images/widget_tab.png',
            'content'     => 'Friends using Surfix',
            'url'         => 'tiles/blank.php',
            'size'        => '2x2',
            'theme'       => 'orange',
            'link'        => ''
        ),
        array(
            'name'        => 'Facebook',
            'thumbnail'   => 'images/facebook.png',
            'content'     => '',
            'url'         => '',
            'size'        => '1x1',
            'theme'       => 'yellow',
            'colour'      => '#3B5B99',
            'link'        => 'http://facebook.com/MelonHTML5'
        ),
        array(
            'name'        => 'Twitter',
            'thumbnail'   => 'images/twitter.png',
            'content'     => '',
            'url'         => '',
            'size'        => '1x1',
            'theme'       => 'yellow',
            'colour'      => '#00ACED',
            'link'        => 'http://twitter.com/MelonHTML5'
        ),
        array(
            'name'        => 'CodeCanyon',
            'thumbnail'   => 'images/linkedin.png',
            'content'     => '',
            'url'         => '',
            'size'        => '1x1',
            'theme'       => 'yellow',
            'colour'      => '#232323',
            'link'        => 'http://codecanyon.net/user/MelonHTML5/portfolio'
        ),
        array(
            'name'        => 'Feed',
            'thumbnail'   => 'images/feed.png',
            'content'     => '',
            'url'         => '',
            'size'        => '1x1',
            'theme'       => 'yellow',
            'link'        => 'http://twitter.com/MelonHTML5'
        )
    );

    function makeRandomTile($id, $size = '2x2') {
        $tile = array(
            'name'        => 'widget_000' . $id,
            'thumbnail'   => '',
            'content'     => '',
            'url'         => 'tiles/blank.php',
            'size'        => $size,
            'theme'       => 'grey',
            'link'        => ''
        );

        return $tile;
    }

    $blog_tile = array(
        'name'        => 'Blog',
        'thumbnail'   => 'images/widget_blog.png',
        'content'     => 'Blog',
        'url'         => 'tiles/blog.php',
        'size'        => '2x2',
        'theme'       => 'orange',
        'link'        => ''
    );

    $aboutme_tile = array(
        'name'        => 'About',
        'thumbnail'   => 'images/widget_aboutme.png',
        'content'     => 'About Us',
        'url'         => 'tiles/about.php',
        'size'        => '2x2',
        'theme'       => 'darkred',
        'link'        => ''
    );


    $tile_container1 = array(
        'size'  => 'full',
        'theme' => '',
        'tiles' => $tiles
    );
    $tile_container2 = array(
        'size'  => 'half',
        'theme' => '',
        'tiles' => array($blog_tile, $aboutme_tile, makeRandomTile(24), makeRandomTile(25), makeRandomTile(26), makeRandomTile(27))
    );
    $tile_container3 = array(
        'size'  => 'half',
        'theme' => '',
        'tiles' => array(makeRandomTile(31), makeRandomTile(32), makeRandomTile(33), makeRandomTile(34), makeRandomTile(35), makeRandomTile(36))
    );

    $ui->addTileContainer($tile_container1);
    $ui->addTileContainer($tile_container2);
    $ui->addTileContainer($tile_container3);

    $ui->printHeader();
    $ui->printTiles();
    $ui->printFooter();
?>