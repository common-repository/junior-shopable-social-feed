(function( $ ) {
'use strict'; 

    $(document).ready( function( $ ) {  
        //console.log( 'Plugin by Junior' );
        if ( typeof allProducts != 'undefined' ) {
            //test localised values
            /*
            console.log( jrigf_variables_array.jrigfFeedInsertPoint );
            console.log( jrigf_variables_array.jrigfTitle );
            console.log( jrigf_variables_array.jrigfFill );
            console.log( jrigf_variables_array.jrigfToken );
            console.log( allProducts );
            */
            var jrigfOutput;
            var shopableProductUrl;

            window.fbAsyncInit = function() {
                FB.init({
                  appId      : '427895737895864',
                  cookie     : true,// Enable cookies to allow the server to access the session.
                  xfbml      : true,// Parse social plugins on this webpage.
                  version    : 'v4.0'// Use this Graph API version for this call.
                });

                mediaAPI( jrigf_variables_array.jrigfToken );
            };

            (function(d, s, id) {// Load the SDK asynchronously
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));


            function mediaAPI( mediaAccessToken ) {// Testing Graph API after login.  See statusChangeCallback() for when this call is made.

                //console.log('Fetching your media.... ');

                $.ajax({
                        url: 'https://graph.instagram.com/me/media?fields=caption,media_url,thumbnail_url&access_token='+mediaAccessToken,
                        beforeSend: function(xhr) {
                             xhr.setRequestHeader("Authorization", "Bearer 6QXNMEMFHNY4FJ5ELNFMP5KRW52WFXN5")
                        }, success: function(data){
                            //console.log(data);
                            //process the JSON data etc
                            getMedia(data);
                        }
                });
                //get the instagram content
                //
                //media function
                function getMedia(media) {

                jrigfOutput = '<div id="jr-instagram-feed" class="jr-instagram-feed-clearfix">';
                if( jrigf_variables_array.jrigfTitle != '' ) {
                    jrigfOutput += '<h2 class="h2" style="text-align:center;">';
                    jrigfOutput += jrigf_variables_array.jrigfTitle;
                    jrigfOutput += '</h2>';   
                }
                jrigfOutput += '<ul>';

                //console.log('Getting media...');
                /*
                console.log(JSON.stringify(media));
                console.log('media.data: '+media.data);
                console.log('media.data[0]: '+media.data[0]);
                console.log('media.data[0].caption: '+media.data[0].caption);
                */                      
                //
                var mediaLength = media.data.length;                    
                if( mediaLength > jrigf_variables_array.jrigfCount ) {
                    mediaLength = jrigf_variables_array.jrigfCount; 
                }   
                //console.log('mediaLength: '+mediaLength);
                //
                for (var i = 0; i <= mediaLength-1; i++) {
                    //console.log('count: '+i);
                  /*
                  console.log('media.data[i].caption: '+media.data[i].caption);
                  console.log('media.data[i].media_url: '+media.data[i].media_url);
                  */
                  /*
                  jrigfOutput += '<li><div class="jr-instagram-feed-post-padding"><div class="shopable jr-instagram-feed-post-inner" style="background: url('+media.data[i].media_url+'); background-size: cover; background-position: center; background-repeat: no-repeat;" alt="'+media.data[i].caption+'"></div></div></li>';
                  */
                var shopTagStart = '#';//open quote
                var shopTagEnd = ' ';//close quote 

                var searchingString = media.data[i].caption;

                if(typeof searchingString != 'undefined') {

                } else {
                    searchingString = '';
                }

                searchingString = searchingString.replace(/"/g, "'");

                var mediaUrl = media.data[i].media_url;
                var mediaType = media.data[i].media_type;
                var thumbnailUrl = media.data[i].thumbnail_url;

                //console.log('This images caption is: '+searchingString);

                //
                var foundShopTagStart = searchingString.match(shopTagStart);
                var foundShopTagEnd = searchingString.match(shopTagEnd);
                //
                if(foundShopTagStart != null) {
                    //console.log('tag found');
                    var firstSplitSearchingString = searchingString.split(shopTagStart);
                    //console.log('firstSplitSearchingString: '+firstSplitSearchingString);
                    var secondSplitSearchingString = firstSplitSearchingString[1].split(shopTagEnd);
                    //console.log('secondSplitSearchingString: '+secondSplitSearchingString);
                    var shopableProduct = secondSplitSearchingString[0];
                    shopableProduct = shopableProduct.toLowerCase();
                    //console.log('shopableProduct: '+shopableProduct);
                    //get url
                    //console.log(allProducts);
                    //
                    var allProductsSearch = $.grep(allProducts, function(e){ return e.name == shopableProduct; });
                    //didn't find in the array
                    if (allProductsSearch.length == 0) {
                      //console.log('no products boring style...');
                      buildBasicPost(thumbnailUrl,mediaType,mediaUrl,searchingString)

                    } 
                    //found the product
                    else if (allProductsSearch.length == 1) {
                      //console.log('found the product...its shoppable');
                      shopableProductUrl = allProductsSearch[0].url;
                      //console.log('shopableProductUrl: '+shopableProductUrl);
                      buildShoppablePost(thumbnailUrl,mediaType,mediaUrl,searchingString);
                    }
                    //found multiple some how
                    else {
                      //console.log('too many products error');
                      buildBasicPost(thumbnailUrl,mediaType,mediaUrl,searchingString);
                    }
                } else {				
                    //console.log('no tags');
                    buildBasicPost(thumbnailUrl,mediaType,mediaUrl,searchingString);		
                }
                }
                //
                jrigfOutput += '</ul></div>';
                //
                var update = document.getElementById('jr-instagram-feed');
                //update.innerHTML = jrigfOutput;
                //$('.instagram-feed-title').removeClass('hide');
                //shopable();
                //
                if( $('#jr-instagram-feed-wrap').length != 0 ) {
                  var jrInsertPoint = '#jr-instagram-feed-wrap';
                } else {
                  var jrInsertPoint = jrigf_variables_array.jrigfFeedInsertPoint;
                }
                //console.log('jrInsertPoint: '+jrInsertPoint);
                $( jrInsertPoint ).append(jrigfOutput); 

             }
                //
                //make basic post
                function buildBasicPost(thumbnailUrl,mediaType,mediaUrl,searchingString) {
                    if( mediaType != 'VIDEO' ) {
                        jrigfOutput += '<li><div class="jr-instagram-feed-post-padding"><div class="jr-instagram-feed-post-inner" style="background: url('+mediaUrl+'); background-size: cover; background-position: center; background-repeat: no-repeat;" alt="'+searchingString+'"></div></div></li>';
                    } else {
                        //video
                        jrigfOutput += '<li><div class="jr-instagram-feed-post-padding"><div class="jr-instagram-feed-post-inner" style="background: url('+thumbnailUrl+'); background-size: cover; background-position: center; background-repeat: no-repeat;" alt="'+searchingString+'"></div></div></li>';
                    }
                }
                //make shopable post
                function buildShoppablePost(thumbnailUrl,mediaType,mediaUrl,searchingString) {
                    if( mediaType != 'VIDEO' ) {
                        jrigfOutput += '<li><div class="jr-instagram-feed-post-padding"><a href="'+shopableProductUrl+'" class="jr-shopable-link"><div class="jr-instagram-feed-post-inner" style="background: url('+mediaUrl+'); background-size: cover; background-position: center; background-repeat: no-repeat;" alt="'+searchingString+'"></div><svg style="fill:'+jrigf_variables_array.jrigfFill+';" aria-hidden="true" focusable="false" role="presentation" class="jr-shopable-icon icon icon-cart icon-cart-instagram" viewBox="0 0 37 40"><path d="M36.5 34.8L33.3 8h-5.9C26.7 3.9 23 .8 18.5.8S10.3 3.9 9.6 8H3.7L.5 34.8c-.2 1.5.4 2.4.9 3 .5.5 1.4 1.2 3.1 1.2h28c1.3 0 2.4-.4 3.1-1.3.7-.7 1-1.8.9-2.9zm-18-30c2.2 0 4.1 1.4 4.7 3.2h-9.5c.7-1.9 2.6-3.2 4.8-3.2zM4.5 35l2.8-23h2.2v3c0 1.1.9 2 2 2s2-.9 2-2v-3h10v3c0 1.1.9 2 2 2s2-.9 2-2v-3h2.2l2.8 23h-28z"></path></svg></a></div></li>';
                    } else {
                        //video
                        jrigfOutput += '<li><div class="jr-instagram-feed-post-padding"><a href="'+shopableProductUrl+'" class="jr-shopable-link"><div class="jr-instagram-feed-post-inner" style="background: url('+thumbnailUrl+'); background-size: cover; background-position: center; background-repeat: no-repeat;" alt="'+searchingString+'"></div><svg style="fill:'+jrigf_variables_array.jrigfFill+';" aria-hidden="true" focusable="false" role="presentation" class="jr-shopable-icon icon icon-cart icon-cart-instagram" viewBox="0 0 37 40"><path d="M36.5 34.8L33.3 8h-5.9C26.7 3.9 23 .8 18.5.8S10.3 3.9 9.6 8H3.7L.5 34.8c-.2 1.5.4 2.4.9 3 .5.5 1.4 1.2 3.1 1.2h28c1.3 0 2.4-.4 3.1-1.3.7-.7 1-1.8.9-2.9zm-18-30c2.2 0 4.1 1.4 4.7 3.2h-9.5c.7-1.9 2.6-3.2 4.8-3.2zM4.5 35l2.8-23h2.2v3c0 1.1.9 2 2 2s2-.9 2-2v-3h10v3c0 1.1.9 2 2 2s2-.9 2-2v-3h2.2l2.8 23h-28z"></path></svg></a></div></li>';
                    }
                }
            }   
        }      
    } );
})( jQuery );