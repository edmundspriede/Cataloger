<div class="catalog_item"><div class="item_images"><img id="item_image" name="item_image"  src="{$image_1_url}" alt="{title}" title="{title}" /><div class="item_thumbnails">{section name=ind loop=$image_url_array}<a href="javascript:repl('{$image_url_array[ind]}')"><img src="{$image_thumb_url_array[ind]}" title="{title}" alt="{title}" /></a>{/section}</div></div>{section name=at loop=$attrlist}<div class="item_attribute_name">{$attrlist[at].name}:</div><div class="item_attribute_val">{eval var=$attrlist[at].key}</div>{/section}<script type="text/javascript">function repl(img){  document.item_image.src=img;}</script>{if $file_count > 0}<ul class="files">{section name=ind loop=$file_name_array}<li><a href="{$file_url_array[ind]}">{$file_name_array[ind]}</a></li>{/section}</ul>{/if}</div>