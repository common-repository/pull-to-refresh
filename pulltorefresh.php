<?php

/**
 * Plugin Name: Pull to Refresh
 * Plugin URI: http://wordpress.org/plugins/pulltorefresh/
 * Description: Pull To Refresh for the web. Pull to refresh for Wordpress is cool plugin that adds Pull to Refresh feature for your web page. Pull to refresh is a popular feature on our mobile devices/Apps that refreshes the current page/screen by pulling down on the view. It is an ultra-intuitive way of refreshing the displayed page content by simply pulling the page down with your thumb, then releasing it — sort of like pulling a lever on a slot machine. It is simple and easy to do!
 * Version: 1.1
 * Author: JoomlaForce Team
 * Author URI: http://www.joomlaforce.com
 * Copyright   Copyright (C) 2014. All rights reserved.
 * License     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
 

	

//Funzione Comune per richiamare javascript e css insieme!
function jfptr_all_jscss() {
//Load JS and CSS files in here

//Per JS
wp_register_script ('placeholder', get_stylesheet_directory_uri() . '/js/placeholder.js', array( 'jquery' ),'1',true);
wp_enqueue_script('placeholder');

//Per Css
wp_register_style ('googlefonts', 'http://fonts.googleapis.com/css?family=Hammersmith+One', array(),'2','all');
wp_enqueue_style( 'googlefonts');

/* SOLO SE IN HOME PAGE if ( is_front_page() ) {
  wp_enqueue_script('smoothscroll');
  }
*/
}



//ALTRO ESEMPIO BEST WAY
function jfptr_addhead_scripts() {
//wp_register_script('jfptr_hammer', plugins_url('/assets/js/hammer.js', __FILE__), array('jquery'),'1.1', true);
//wp_register_script('jfptr_modernizr', plugins_url('/assets/js/modernizr.js', __FILE__), array('jquery'),'1.1', true);

wp_register_script('jfptr_hammer', plugins_url('/assets/js/hammer.js', __FILE__));
wp_register_script('jfptr_modernizr', plugins_url('/assets/js/modernizr.js', __FILE__));

wp_enqueue_script('jfptr_hammer');
wp_enqueue_script('jfptr_modernizr');
}

function jfptr_addhead_styles() {
//wp_register_script('my_stylesheet', plugins_url('my-stylesheet.css', __FILE__));
//wp_enqueue_script('my_stylesheet');

wp_register_style( 'hammerstyle', plugins_url('/assets/css/hammer.css', __FILE__), array(), '1.0', 'all' );
wp_enqueue_style( 'hammerstyle' );

}
//echo plugins_url('amazing_script.js', __FILE__);

add_action( 'wp_enqueue_scripts', 'jfptr_addhead_scripts' );  
add_action( 'wp_enqueue_scripts', 'jfptr_addhead_styles' );  


//Nuovo Hook semvrA funzionante !!!

    add_filter('template_include','jfptr_custom_include',1);
    function jfptr_custom_include($template) {
            ob_start();
            return $template;
    }
     
    add_filter('shutdown','jfptr_body_code',0);
	function jfptr_body_code() {
		
					 $js = "<script>
				  		var PullToRefresh = (function() {
					function Main(container, slidebox, slidebox_icon, handler) {
						var self = this;
						this.breakpoint = 10;
			
						this.container = container;
						this.slidebox = slidebox;
						this.slidebox_icon = slidebox_icon;
						this.handler = handler;
			
						this._slidedown_height = 0;
						this._anim = null;
						this._dragged_down = false;
			
						this.hammertime = Hammer(this.container)
							//.on('touch dragdown release', function(ev) {
							.on('dragdown release', function(ev) {
								self.handleHammer(ev);
							});
					}
			
			
					/**
					 * @param ev
					 */
					Main.prototype.handleHammer = function(ev) {
						var self = this;
			
						switch(ev.type) {
							// reset element on start
							case 'touch':
								this.hide();
								break;
			
							// on release we check how far we dragged
							case 'release':
								if(!this._dragged_down) {
									return;
								}
			
								// cancel animation
								cancelAnimationFrame(this._anim);
			
								// over the breakpoint, trigger the callback
								if(ev.gesture.deltaY >= this.breakpoint) {
									container_el.className = 'pullrefresh-loading';
									pullrefresh_icon_el.className = 'icon loading';
			
									this.setHeight(60);
									this.handler.call(this);
								}
								// just hide it
								else {
									pullrefresh_el.className = 'slideup';
									container_el.className = 'pullrefresh-slideup';
			
									this.hide();
								}
								break;
			
							// when we dragdown
							case 'dragdown':
								// if we are not at the top move down
								var scrollY = window.scrollY;
								if(scrollY > 5) {
									return;
								} else if(scrollY !== 0) {
									window.scrollTo(0,0);
								}
			
								this._dragged_down = true;
			
								// no requestAnimationFrame instance is running, start one
								if(!this._anim) {
									this.updateHeight();
								}
			
								// stop browser scrolling
								ev.gesture.preventDefault();
			
								// update slidedown height
								// it will be updated when requestAnimationFrame is called
								this._slidedown_height = ev.gesture.deltaY * 0.4;
								break;
						}
					};
			
			
					/**
					 * when we set the height, we just change the container y
					 * @param   {Number}    height
					 */
					Main.prototype.setHeight = function(height) {
						if(Modernizr.csstransforms3d) {
							this.container.style.transform = 'translate3d(0,'+height+'px,0) ';
							this.container.style.oTransform = 'translate3d(0,'+height+'px,0)';
							this.container.style.msTransform = 'translate3d(0,'+height+'px,0)';
							this.container.style.mozTransform = 'translate3d(0,'+height+'px,0)';
							this.container.style.webkitTransform = 'translate3d(0,'+height+'px,0) scale3d(1,1,1)';
						}
						else if(Modernizr.csstransforms) {
							this.container.style.transform = 'translate(0,'+height+'px) ';
							this.container.style.oTransform = 'translate(0,'+height+'px)';
							this.container.style.msTransform = 'translate(0,'+height+'px)';
							this.container.style.mozTransform = 'translate(0,'+height+'px)';
							this.container.style.webkitTransform = 'translate(0,'+height+'px)';
			
						}
			
			
						else {
							this.container.style.top = height+'px';
						}
					};
			
			
					/**
					 * hide the pullrefresh message and reset the vars
					 */
					Main.prototype.hide = function() {
						container_el.className = '';
						this._slidedown_height = 0;
						this.setHeight(0);
						cancelAnimationFrame(this._anim);
						this._anim = null;
						this._dragged_down = false;
					};
			
			
					/**
					 * hide the pullrefresh message and reset the vars
					 */
					Main.prototype.slideUp = function() {
						var self = this;
						cancelAnimationFrame(this._anim);
			
						pullrefresh_el.className = 'slideup';
						container_el.className = 'pullrefresh-slideup';
			
						this.setHeight(0);
			
						setTimeout(function() {
							self.hide();
						}, 500);
					};
			
			
					/**
					 * update the height of the slidedown message
					 */
					Main.prototype.updateHeight = function() {
						var self = this;
			
						this.setHeight(this._slidedown_height);
			
						if(this._slidedown_height >= this.breakpoint){
							//this.slidebox.className = 'breakpoint';
							container_el.className = 'pullrefresh-breakpoint';
							this.slidebox_icon.className = 'icon arrow arrow-up';
						}
						else {
							this.slidebox.className = '';
							this.slidebox_icon.className = 'icon arrow';
						}
			
						this._anim = requestAnimationFrame(function() {
							self.updateHeight();
						});
					};
			
					return Main;
				})();
			
			
			
				function getEl(id) {
					return document.getElementById(id);
				}
			
				var container_el = getEl('container');
				var pullrefresh_el = getEl('pullrefresh');
				var pullrefresh_icon_el = getEl('pullrefresh-icon');
			
				var refresh = new PullToRefresh(container_el, pullrefresh_el, pullrefresh_icon_el);
			
				// refresh page
				refresh.handler = function() {
					var self = this;
					// a small timeout to demo the loading state
					setTimeout(function() {
						var preload = new Image();
						preload.onload = function() {
							self.slideUp();
						};
						 window.location.reload(true);
					}, 1000);
					
				};</script>
			";
		         	
					 //$js="";	
			$body_start_code .="<div id='container'><div id='pullrefresh'><div class='message'><div id='pullrefresh-icon' class='icon arrow arrow-down'></div> <span></span> </div></div>".$js;

            $content = ob_get_clean();
            $content = preg_replace('#<body([^>]*)>#i',"<body$1>{$body_start_code}",$content);
            echo $content;
    }




 

?>