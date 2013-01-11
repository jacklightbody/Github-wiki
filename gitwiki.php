<?php
    class Gitwiki {
        public function __construct($githubWikiUrl, $currentPath='/'){
            $urlArr=explode('/', $githubWikiUrl);
            if(end($urlArr)!='wiki'){
                throw new Exception('Invalid Wiki Url.');
            }
            $this->path=$currentPath;
            $this->github=$githubWikiUrl;
            $this->githubArr=$urlArr;
        }
        public function getWiki(){
            if(isset($this->path)&&$this->path!='/'){
                $c = curl_init($this->github.'/'.$this->path);
                curl_setopt($c,  CURLOPT_RETURNTRANSFER, TRUE);
                $html = curl_exec($c);
                $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
                curl_close($c);
                if($httpCode == 302||$httpCode == 404) {
                    //they want us to create a new page or its not found
                    $html='Page not found. <a href="?wikiurl=/">Back to documentation home page.</a>';
                    return $html;
                }
                $html=self::process($html);
            }else{
                $c = curl_init($this->github);
                curl_setopt($c,  CURLOPT_RETURNTRANSFER, TRUE);
                $html = curl_exec($c);
                $httpCode = curl_getinfo($c, CURLINFO_HTTP_CODE);
                curl_close($c);
                $html=self::process($html);
            }
            return $html;
        }
        function process($html){
            $doc = new DOMDocument();
            @$doc->loadHTML($html);
            $html= self::get_inner_html($doc->getElementById("wiki-wrapper"));
            end($this->githubArr);
            $repo=prev($this->githubArr);
            $owner=prev($this->githubArr);
            $html=preg_replace('/<ul class="wiki-actions.*\s*.*\s.*\s.*<\/ul>/', '</div><div id="wiki-content">', $html);
            $html=preg_replace('/href="\/'.$owner.'\/'.$repo.'\/wiki\/(.*)"/', "href='?wikiurl=$1'", $html);
            return $html;
        }

        function get_inner_html( $node ) {//from http://www.php.net/manual/en/class.domelement.php#101243
            $innerHTML= '';
            $children = $node->childNodes;
            foreach ($children as $child) {
                $innerHTML .= $child->ownerDocument->saveXML( $child );
            }
            return $innerHTML;
        }
    }
