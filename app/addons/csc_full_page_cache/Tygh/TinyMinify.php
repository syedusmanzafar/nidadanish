<?php
namespace Tygh;
use Tygh\Registry;
if (version_compare(PRODUCT_VERSION, '4.3.0', '>')){
	require_once(\csc_full_page_cache::_("YXBwL2xpYi92ZW5kb3IvdGVkaXZtL2pzaHJpbmsvc3JjL0pTaHJpbmsvTWluaWZpZXIucGhw"));
}
class TinyHtmlMinifier {
    function __construct($options) {
        $this->options = $options;
        $this->output = '';
        $this->build = [];
        $this->skip = 0;
        $this->skipName = '';
        $this->head = false;
        $this->elements = [
            'skip' => [
                'code',
                'pre',
                'textarea',
                'script'				
            ],
            'inline' => [
                'b',
                'big',
                'i',
                'small',
                'tt',
                'abbr',
                'acronym',
                'cite',
                'code',
                'dfn',
                'em',
                'kbd',
                'strong',
                'samp',
                'var',
                'a',
                'bdo',
                'br',
                'img',
                'map',
                'object',
                'q',
                'span',
                'sub',
                'sup',
            ],
            'hard' => [
                '!doctype',				
                'body',
                'html',
            ]
        ];
    }
    // Run minifier
   
	function minify($html) {
		
		if (defined('AJAX_REQUEST')){
			return $html;
		}				
		list($clear_html, $scripts) = $this->remove_scripts($html);	
		$clear_html = $this->removeComments($clear_html);
        $rest = $clear_html;
        while(!empty($rest)) :         
            $parts = explode('<', $rest, 2);
			if (trim($parts[0])){			
            	$this->walk($parts[0]);
			}
            $rest = (isset($parts[1])) ? $parts[1] : '';
        endwhile;		
		$output = $this->output;				
		$output = $this->add_scripts($scripts, $output);
				
        return $output;
    }
	
	function remove_scripts($html){		
		$html = str_replace(
			array('< script', '<  script'), 
			'<script', 
			$html);
		$html = str_replace(
			array('< /script>', '< / script>', '< / script >', '</script >'), 
			'</script>', 
			$html);		
		$scripts = array();
		$clear_html='';
		$script_id=0;		
		$rest=$html;
			
		while (!empty($rest)){
			$parts = explode('</script>', $rest, 2);			
			if (strpos($parts[0], '<script')!==false){
				$script_id ++;
				list($_html, $scripts[$script_id])	= explode('<script', $parts[0], 2);											
				/*Minify JS*/
				if (version_compare(PRODUCT_VERSION, '4.3.0', '>')){							
					if (Registry::get('addons.csc_full_page_cache.minify_js')=="Y" && strpos($scripts[$script_id], 'application/ld+json')===false){						
					  $scripts[$script_id] = \JShrink\Minifier::minify($scripts[$script_id], array(
						  'flaggedComments' => false
					  ));						
					}
				}
				/**/		
				$clear_html .=$_html."{*script_$script_id*}";				
			}else{				
				$clear_html .=$parts[0];						
			}					
            $rest = (isset($parts[1])) ? $parts[1] : '';							
		}
		
		return array($clear_html, $scripts);
	}
	function add_scripts($scripts, $output){
		foreach ($scripts as $script_id=>$script){
			if (Registry::get('addons.csc_full_page_cache.minify_js')=="Y"){
				$script = str_replace('src=', ' src=', $script);
			}
			$script_code="<script ".$script."</script>";	
			$output = str_replace("{*script_$script_id*}", $script_code, $output);
		}		
		
		return 	$output;
	}
	
    // Walk trough html
    function walk(&$part) {
        $tag_parts = explode('>', $part);
        $tag_content = $tag_parts[0];
        
        if(!empty($tag_content)) {
            $name = $this->findName($tag_content);
            $element = $this->toElement($tag_content, $part, $name);
            $type = $this->toType($element);
            if($name == 'head') {
                $this->head = ($type == 'open') ? true : false;
            }
            $this->build[] = [
                'name' => $name,
                'content' => $element,
                'type' => $type
            ];
            $this->setSkip($name, $type);
            if(!empty($tag_content)) {
                $content = (isset($tag_parts[1])) ? $tag_parts[1] : '';
                if($content !== '') {
                    $this->build[] = [
                        'content' => $this->compact($content, $name, $element),
                        'type' => 'content'
                    ];
                }
            }
            $this->buildHtml();
        }
    }
    // Remove comments
    function removeComments($content = '') {
		//return $content;		
        return preg_replace('/(?!<!--fpc)(?!<!--end_fpc)<!--(.|\s)*?-->/', '', $content);
		
    }
    // Check if string contains string
    function contains($needle, $haystack) {
        return strpos($haystack, $needle) !== false;
    }
    // Return type of element
    function toType($element) {
        $type = (substr($element, 1, 1) == '/') ? 'close' : 'open';
        return $type;
    }
    // Create element
    function toElement($element, $noll, $name) {
        $element = $this->stripWhitespace($element);
        $element = $this->addChevrons($element, $noll);
        $element = $this->removeSelfSlash($element);
        $element = $this->removeMeta($element, $name);
        return $element;
    }
    // Remove unneeded element meta
    function removeMeta($element, $name) {
        if($name == 'style') {
            $element = str_replace([
                ' type="text/css"',
                "' type='text/css'"
            ],
            ['', ''], $element);
        } elseif($name == 'script') {
            $element = str_replace([
                ' type="text/javascript"',
                " type='text/javascript'"
            ],
            ['', ''], $element);
        }
        return $element;
    }
    // Strip whitespace from element
    function stripWhitespace($element) {

        if($this->skip == 0) {
            $element = preg_replace('/\s+/', ' ', $element);
        }
        return $element;
    }
    // Add chevrons around element
    function addChevrons($element, $noll) {
        $char = ($this->contains('>', $noll)) ? '>' : '';
        $element = '<' . $element . $char;
        return $element;
    }
    // Remove unneeded self slash
    function removeSelfSlash($element) {
        if(substr($element, -3) == ' />') {
            $element = substr($element, 0, -3) . '>';
        }
        return $element;
    }
    // Compact content
    function compact($content, $name, $element) {
        if($this->skip != 0) {
            $name = $this->skipName;
        } else {
            $content = preg_replace('/\s+/', ' ', $content);
        }
        if(
            $this->isSchema($name, $element) &&
            !empty($this->options['collapse_json_ld'])
            ) {
            return json_encode(json_decode($content));
        } if(in_array($name, $this->elements['skip'])) {
            return $content;
        } elseif(
            in_array($name, $this->elements['hard']) ||
            $this->head
            ) {
            return $this->minifyHard($content);
        } else {
            return $this->minifyKeepSpaces($content);
        }
    }
    function isSchema($name, $element) {
        if($name != 'script') return false;
        $element = strtolower($element);
        if($this->contains('application/ld+json', $element)) return true;
        return false;
    }
    // Build html
    function buildHtml() {
        foreach($this->build as $build) {
            if(!empty($this->options['collapse_whitespace'])) {
                
                if(strlen(trim($build['content'])) == 0)
                    continue;
                
                elseif($build['type'] != 'content' && !in_array($build['name'], $this->elements['inline']))
                    trim($build['content']);
                
            }
            $this->output .= $build['content'];
        }
        $this->build = [];
    }
    // Find name by part
    function findName($part) {
        $name_cut = explode(" ", $part, 2)[0];
        $name_cut = explode(">", $name_cut, 2)[0];
        $name_cut = explode("\n", $name_cut, 2)[0];
        $name_cut = preg_replace('/\s+/', '', $name_cut);
        $name_cut = strtolower(str_replace('/', '', $name_cut));
        return $name_cut;
    }
    // Set skip if elements are blocked from minification
    function setSkip($name, $type) {
        foreach($this->elements['skip'] as $element) {
            if($element == $name && $this->skip == 0) {
                $this->skipName = $name;
            }
        }
        if(in_array($name, $this->elements['skip'])) {
            if($type == 'open') {
                $this->skip++;
            }
            if($type == 'close') {
                $this->skip--;
            }
        }
    }
    // Minify all, even spaces between elements
    function minifyHard($element) {
        $element = preg_replace('!\s+!', ' ', $element);
        $element = trim($element);
        return trim($element);
    }
    // Strip but keep one space
    function minifyKeepSpaces($element) {
        return preg_replace('!\s+!', ' ', $element);
    }
}
class TinyMinify {
    static function html($html, $options = []) {
        $minifier = new TinyHtmlMinifier($options);
        return $minifier->minify($html);
    }
}
