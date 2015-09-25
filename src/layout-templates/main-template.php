<!DOCTYPE html><!--[if lt IE 9]><html class="no-js lt-ie9" lang="en" dir="ltr"><![endif]--><!--[if gt IE 8]><!-->
<html class="no-js" lang="en" dir="ltr">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        
        <!-- 
            Web Experience Toolkit (WET) / Boîte à outils de l'expérience Web (BOEW)
            wet-boew.github.io/wet-boew/License-en.html / wet-boew.github.io/wet-boew/Licence-fr.html 
        -->
        
        <title>Site Title - Page Title Goes Here ....</title>
        
        <meta content="width=device-width,initial-scale=1" name="viewport">
        
        <!-- Meta data -->
        <meta name="description" content="Current Page Description Goes Here....">
        <meta name="dcterms.title" content="Site Title - Page Title Goes Here ....">
        <meta name="dcterms.creator" content="Name of the Content Author Goes Here .....">
        <meta name="dcterms.issued" title="W3CDTF" content="Date published (YYYY-MM-DD) Goes Here .....">
        <meta name="dcterms.modified" title="W3CDTF" content="Date modified (YYYY-MM-DD) Goes Here .....">
        <meta name="dcterms.subject" title="scheme" content="Subject Terms / Keywords  Goes Here .....">
        <meta name="dcterms.language" title="ISO639-2" content="eng">
        <!-- Meta data-->
        
        <!--[if gte IE 9 | !IE ]><!-->
            <link href="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/assets/favicon.ico" rel="icon" type="image/x-icon">
            <link rel="stylesheet" href="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/css/theme.min.css">
        <!--<![endif]-->
        <!--[if lt IE 9]>
            <link href="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/assets/favicon.ico" rel="shortcut icon" />
            <link rel="stylesheet" href="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/css/ie8-theme.min.css" />
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
            <script src="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/wet-boew/js/ie8-wet-boew.min.js"></script>
        <![endif]-->
        
        <noscript><link rel="stylesheet" href="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/wet-boew/css/noscript.min.css" /></noscript>
    </head>
    
    <body vocab="http://schema.org/" typeof="WebPage">
        
        <ul id="wb-tphp">
            <li class="wb-slc">
                <a class="wb-sl" href="#wb-cont">Skip to main content</a>
            </li>
            <li class="wb-slc visible-sm visible-md visible-lg">
                <a class="wb-sl" href="#wb-info">Skip to "About this site"</a>
            </li>
            <li class="wb-slc visible-md visible-lg">
                <a class="wb-sl" href="#wb-sec">Skip to section menu</a>
            </li>
        </ul>
        
        <header role="banner">
            <div id="wb-bnr">
                <div id="wb-bar">
                    <div class="container">
                        <div class="row">
                            <object id="gcwu-sig" type="image/svg+xml" tabindex="-1" role="img" data="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/assets/sig-en.svg" aria-label="Government of Canada"></object>
                            <ul id="gc-bar" class="list-inline">
                                <li><a href="http://www.canada.ca/en/index.html" rel="external">Canada.ca</a></li>
                                <li><a href="http://www.canada.ca/en/services/index.html" rel="external">Services</a></li>
                                <li><a href="http://www.canada.ca/en/gov/dept/index.html" rel="external">Departments</a></li>
                                <li id="wb-lng">
                                    <h2>Language selection</h2>
                                    <ul class="list-inline">
                                        <li><a lang="fr" href="content-secmenu-fr.html">Français</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <section class="wb-mb-links col-xs-12 visible-sm visible-xs" id="wb-glb-mn">
                                <h2>Search and menus</h2>
                                <ul class="pnl-btn list-inline text-right">
                                    <li>
                                        <a href="#mb-pnl" title="Search and menus" aria-controls="mb-pnl" class="overlay-lnk btn btn-sm btn-default" role="button">
                                            <span class="glyphicon glyphicon-search">
                                                <span class="glyphicon glyphicon-th-list">
                                                    <span class="wb-inv">
                                                        Search and menus
                                                    </span>
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <div id="mb-pnl"></div>
                            </section>
                        </div> <!-- <div class="row"> -->
                    </div> <!-- <div class="container"> -->
                </div> <!-- <div id="wb-bar"> -->
                
                <div class="container">
                    <div class="row">
                        <div id="wb-sttl" class="col-md-5">
                            <a href="#">
                                <span>Site Title Goes Here .... </span>
                            </a>
                        </div>
                        
                        <object id="wmms" type="image/svg+xml" tabindex="-1" role="img" data="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/assets/wmms.svg" aria-label="Symbol of the Government of Canada"></object>
                        
                        <section id="wb-srch" class="visible-md visible-lg">
                            <h2>Search</h2>
                            <form action="https://google.ca/search" method="get" role="search" class="form-inline">
                                <div class="form-group">
                                    <label for="wb-srch-q">Search website</label>
                                    <input id="wb-srch-q" class="form-control" name="q" type="search" value="" size="27" maxlength="150">
                                    <input type="hidden" name="q" value="site:wet-boew.github.io OR site:github.com/wet-boew/">
                                </div>
                                <button type="submit" id="wb-srch-sub" class="btn btn-default">Search</button>
                            </form>
                        </section>
                    </div> <!-- <div class="row"> -->
                </div> <!-- <div class="container"> -->
            </div> <!-- <div id="wb-bnr"> -->
            
            <nav role="navigation" id="wb-sm" data-trgt="mb-pnl" class="wb-menu visible-md visible-lg" typeof="SiteNavigationElement">
                <div class="pnl-strt container visible-md visible-lg nvbar">
                    <h2>Topics menu</h2>
                    <div class="row">
                        <ul class="list-inline menu" role="menubar">
                            <li><a href="#project" class="item">WET project</a>
                                <ul class="sm list-unstyled" id="project" role="menu">
                                    <li><a href="http://wet-boew.github.io/v4.0-ci/index-en.html#about">About the Web Experience Toolkit</a></li>
                                    <li><a href="http://wet-boew.github.io/v4.0-ci/index-en.html#benefits">Benefits</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/ref/accolades-en.html">Awards, articles and videos</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/events-en.html">Events</a></li>
                                    <li><a href="https://github.com/wet-boew/wet-boew/">Main project page</a></li>
                                    <li><a href="http://wet-boew.github.io/v4.0-ci/License-en.html">Terms and conditions</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/versions/rdmp-en.html">Roadmap of past and future releases</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/versions/index-en.html">Version history</a></li>
                                    <li><a href="http://www.tbs-sct.gc.ca/ws-nw/index-eng.asp">About the Web Standards</a></li>
                                </ul>
                            </li>
                            <li><a href="#implement" class="item">Implement WET</a>
                                <ul class="sm list-unstyled" id="implement" role="menu">
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/start-en.html">Getting started with WET</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/versions/dwnld-en.html">Download WET</a></li>
                                    <li><a href="http://wet-boew.github.io/v4.0-ci/demos/index-en.html">Working examples</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/index-en.html">Documentation</a></li>
                                </ul>
                            </li>
                            <li><a href="#contribute" class="item">Contribute to WET</a>
                                <ul class="sm list-unstyled" id="contribute" role="menu">
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/bugs-en.html">Filing a bug or an issue</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/pull-en.html">Creating a pull request</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/pull-en.html#test">Testing your code</a></li>
                                    <li><a href="http://wet-boew.github.io/wet-boew/docs/start-en.html#develop">Developing for WET</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav> <!-- <nav role="navigation" id="wb-sm" data-trgt="mb-pnl" class="wb-menu visible-md visible-lg" typeof="SiteNavigationElement"> -->
            
            <nav role="navigation" id="wb-bc" property="breadcrumb">
                <h2>You are here:</h2>
                <div class="container">
                    <div class="row">
                        <ol class="breadcrumb">
                            <li>
                                <a href="http://wet-boew.github.io/v4.0-ci/index-en.html">Home</a>
                            </li>
                            <li>
                                <a href="http://wet-boew.github.io/v4.0-ci/demos/index-en.html">Working examples</a>
                            </li>
                            <li>
                                <a href="index-en.html">GCWU theme</a>
                            </li>
                            <li>Content page - Secondary menu</li>
                        </ol>
                    </div>
                </div>
            </nav> <!-- <nav role="navigation" id="wb-bc" property="breadcrumb"> -->
        </header><!-- <header role="banner"> -->
        
        <div class="container">
            <div class="row">
                <main role="main" property="mainContentOfPage" class="col-md-9 col-md-push-3">
                    <?php echo $content; ?>
                </main>
                <nav role="navigation" id="wb-sec" typeof="SiteNavigationElement" class="col-md-3 col-md-pull-9 visible-md visible-lg">
                    <h2>Section menu</h2>
                    <ul class="list-group menu list-unstyled">
                        <li>
                            <h3 class="wb-navcurr">Content page - Secondary menu</h3>
                            <ul class="list-group menu list-unstyled">
                                <li><a class="list-group-item" href="#">Item&#32;1</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;2</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;3</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;4</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;5</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;6</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;7</a></li>
                                <li><a class="list-group-item" href="#">Item&#32;8</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div> <!-- <div class="container"> contains main content for the current page -->
        
        <footer role="contentinfo" id="wb-info" class="visible-sm visible-md visible-lg wb-navcurr">
            <div class="container">
                <nav role="navigation">
                    <h2>About this site</h2>
                    <ul id="gc-tctr" class="list-inline">
                        <li><a rel="license" href="http://wet-boew.github.io/wet-boew/License-en.html">Terms and conditions</a></li>
                        <li><a href="http://www.tbs-sct.gc.ca/tbs-sct/common/trans-eng.asp">Transparency</a></li>
                    </ul>
                    <div class="row">
                        <section class="col-sm-3">
                            <h3>Contact us</h3>
                            <ul class="list-unstyled">
                                <li><a href="https://github.com/wet-boew/wet-boew/issues/new">Questions or comments?</a></li>
                            </ul>
                        </section>
                        <section class="col-sm-3">
                            <h3>About</h3>
                            <ul class="list-unstyled">
                                <li><a href="http://wet-boew.github.io/v4.0-ci/index-en.html#about">About the Web Experience Toolkit</a></li>
                                <li><a href="http://www.tbs-sct.gc.ca/ws-nw/index-eng.asp">About the Web Standards</a></li>
                            </ul>
                        </section>
                        <section class="col-sm-3">
                            <h3>News</h3>
                            <ul class="list-unstyled">
                                <li><a href="https://github.com/wet-boew/wet-boew/pulse">Recent project activity</a></li>
                                <li><a href="https://github.com/wet-boew/wet-boew/graphs">Project statistics</a></li>
                            </ul>
                        </section>
                        <section class="col-sm-3">
                            <h3>Stay connected</h3>
                            <ul class="list-unstyled">
                                <li><a href="https://twitter.com/WebExpToolkit">Twitter</a></li>
                            </ul>
                        </section>
                    </div>
                </nav>
            </div>
            <div id="gc-info">
                <div class="container">
                    <nav role="navigation">
                        <h2>Government of Canada footer</h2>
                        <ul class="list-inline">
                            <li><a href="http://healthycanadians.gc.ca"><span>Health</span></a></li>
                            <li><a href="http://travel.gc.ca"><span>Travel</span></a></li>
                            <li><a href="http://www.servicecanada.gc.ca/eng/home.shtml"><span>Service Canada</span></a></li>
                            <li><a href="http://www.jobbank.gc.ca"><span>Jobs</span></a></li>
                            <li><a href="http://actionplan.gc.ca/en"><span>Economy</span></a></li>
                            <li id="canada-ca"><a href="http://www.canada.ca/en/index.html">Canada.ca</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </footer>
        
        <!--[if gte IE 9 | !IE ]><!-->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
            <script src="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/wet-boew/js/wet-boew.min.js"></script>
        <!--<![endif]-->
        <!--[if lt IE 9]>
            <script src="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/wet-boew/js/ie8-wet-boew2.min.js"></script>
        <![endif]-->
        <script src="<?php echo $request_obj->getUri()->getBasePath().'/wet4-theme-files'; ?>/theme-gcwu-fegc/js/theme.min.js"></script>
    </body>
</html>