{* Tabs Headers *}
<div id="page_tabs">
  <div id="general">
    What Is It
  </div>    
  <div id="how">
    How To Use It
  </div>    
  <div id="featurelists">
    Feature Lists
  </div>    
  <div id="imagesys">
    Cataloger Image System
  </div>
  <div id="advanced">
    Customization and Advanced Topics
  </div>
  <div id="whatsnew">
    What's new
  </div>
  <div id="about">
    About
  </div>
</div>       
{* Tabs Headers *}

<div class="clearb"></div>

{* Tabs Contents *}
<div id="page_content">
{*************************************************************************************}
{* What Is It *}
  <div id="general_c">
    <h3>What Does This Do?</h3>
    <p>This module lets you create online catalogs or portfolios. Catalogs consist of <strong></strong>"Catalog Items"</strong> which could be products, works of art, or the like, and <strong>"Catalog Categories"</strong> which could be item categories or other natural divisions of the catalog.</p>
    <p>Categories may be defined hierarchically (using the standard CMS Made Simple content list). Category pages can display items in the category and/or subcategories, optionally recursing through sub-categories. This behavior can be configured on a per-category basis.</P>
    <p>There is also a <strong>"Printable Catalog"</strong>, which represents the whole collection of Catalog Items on a single page.</p>
    <p>The module has built-in support for <strong>"Content Aliases"</strong> (a module available at <a href="http://dev.cmsmadesimple.org/projects/contentaliases">the Developer Forge</a>), which allows you to place any Catalog Item into multiple Catalog Categories.</p>
    <p><div class='warning'>As of version 1.0 Cataloger only works on CMSMS 2.x.</div></p>
  </div>
  {*************************************************************************************}
  {* How To Use It *}
  <div id="how_c">
    <h3>How Do I Use It</h3>
    <p>When you install this module, it creates three new Content Types: <strong>Catalog Item</strong>, <strong>Catalog Category</strong>, and <strong>Printable Catalog</strong>. When you're in your site administration category, you add <strong>Catalog Items</strong> and <strong>Catalog Categories</strong> just as you would any other kind of page. Select Content &gt; Pages &gt; Add Content, and then select <strong>"Catalog Item"</strong> or <strong>"Catalog Category"</strong> from the <strong>Content Type pulldown</strong>.</p>
    <h4>Catalog Items</h4>
    <p>Adding a <strong>Catalog Item</strong> is similar to adding an ordinary page to your site. The data fields for the Item are not exactly the same, however. Also note that in the "Images" tab, you can select multiple images to upload for the item. When you upload images, the system will size them appropriately for the catalog, create thumbnails for use in the Item's page, create thumbnails for any Category pages, and so on.</p>
    <p><div class='warning'>See image use below.</div></p>

    <h4>Catalog Categories</h4>
    <p>A <strong>Catalog Category</strong> is used for organizing your catalog items. It provides a page that lists Cataloger content that is contained by it. A Catalog Item is considered to be part of a Catalog Category if it is below that Category in the Site Hierarchy. Categories can similarly include other Categories.</p>
    <p>Catalog Categories have a number of settings to determine how they should display the Items and Categories they contain: if you look at the <strong>"Options"</strong> tab, you can choose how many items and/or categories to show, what order to show them in, whether to display only items or only categories or both, and how many levels of the hierarchy to display below the category page.</p>
    <br>
    <p>For example, consider the following structure:</p>
    <ol>
      <li><b>Hats</b>
        <ol>
          <li><b>Fashion</b>
             <ol>
               <li>Feathered Hat</li><li>Fedora </li><li>Baseball Hat</li>
             </ol>
           </li>
           <li><b>Work Hats</b>
              <ol>
                <li>Hard Hat</li><li>Diving Helmet</li>
              </ol>
            </li>
        </ol>
      </li>
    </ol>
    <p>In this diagram, items in bold are Catalog Categories, all other items are Catalog Items. Depending upon how you set your options, the <strong>Hats</strong> Category Page can show only entries for the <strong>Fashion</strong> and <strong>Work Hats</strong> Category Pages, or it can show entries for <strong>Fashion</strong>, <strong>Work Hats</strong>, and all the hats listed below them.</p>
    <p>You can opt to show the items in "natural" order (i.e., the order the Items show up in the CMS Content Hierarchy), or in alphabetical order.</P>
    <p>If you have large numbers of items in a Category, you can set the maximum number to be displayed in a Category Page, and the page will automatically create links to navigate through the list.</p>
    <p>Each Category page can have different settings for sort order, levels to display, and number of items. You can set the default for these values in <strong><em>Extensions &gt; Cataloger &gt; Manage Preferences</em></strong>. If, at a later date, you wish to change some setting on all of your Category pages, you can go into <strong><em>Extensions &gt; Cataloger &gt; Global Catalog Options</em></strong>, and make those changes.</p>
    <h4>Images</h4>
    <p>Cataloger allows you to select different size images for Item pages, Category pages, and the Printable Catalog. You can set these defaults in <strong><em>Extensions &gt; Cataloger &gt; Manage Preferences</em></strong>.</p>
    <p>When you upload images, the original is stored. When someone visits a page, the reduced size images are requested using a special URL, which will load the scaled image if it exists, creating it if it doesn't. This allows you to change the size of images, without having to re-upload all the images, or rescaling them all at once.</p>
    <p><div class='warning'>The image rescaling code requires that you have either ImageMagick or GD lib installed.</div></p>
    <p><div class='information'>Currently, only jpeg format images are supported.</div></p>
    <h4>Files</h4>
    <p>Cataloger allows you to associate arbitrary files with any Item page. These are uploaded via the admin, just like images.
    <p><div class='warning'>There have been rudimentary security considerations (e.g., file-extension-based restrictions), however, as with any file upload capability that puts files inside the web root, if you do not trust your uploaders, you are at some degree of risk. You have been warned.</div></p>
  </div>
  {*************************************************************************************}
  {* Feature Lists *}
  <div id="featurelists_c">
    <h3>Feature Lists</h3>
    <p>You can, as of <strong>version 0.4</strong>, have <strong>"feature lists"</strong> which are the <strong><em>n</em></strong> most-recently added catalog items, or a collection of <strong><em>k</em></strong> random items from the catalog.</p>
    <h4>"All" Feature List</h4>
    <p>To get a list of all catalog items (optionally under a specified part of your hierarchy) for processing via a template, you can use
    the <strong>"all"</strong> feature list.</p>
    <p>The syntax for an <strong>"all"</strong> list is like:</p>
    <pre><strong>&#123;Cataloger action='all' sub_template='my_sub_template'}</strong></pre>
    <p>where sub_template is the template to use to render the list.</p>
    <p>There is an optional parameter, <strong>alias='page_alias'</strong>, where page_alias indicates the top of the tree (e.g., a place in your menu
    hierarchy, typically a category page) in which to look for new items. A special value for <strong>"page_alias"</strong> is <strong>"/"</strong>, which means to start at the root.</p>
    <p>This is useful for catalog-wide operations. For example, to get a list of all categories (and do something with them),
    you could use the tag:</p> 
    <pre><strong>&#123;Cataloger action='all' sub_template='my_sub_template' alias='/' recurse='categories_all'}</strong></pre>   
    <p>Your template could then throw those categories into a pulldown menu that redirected you to that category's page, for example:</p>
    <pre>
    &lt;select name="foo" onchange="document.location=this.options[this.selectedIndex].value;">
    &lt;option value="">Select A target&lt;/option>
    &#123;foreach from=$items item=thisone}
       &lt;option value="&#123;$thisone.link}">&#123;$thisone.title}&lt;/option>
    &#123;/foreach}
    &lt;/select></pre>
    <p>If you wanted that to only show categories that were in the very top level of your hierarchy, you'd replace the <strong>recurse='categories_all'</strong> with <strong>recurse='categories_one'</strong>. Or if you wanted it to be all catalog Items instead of categories, you could use a tag like:</p>
    <pre><strong>&#123;Cataloger action='all' sub_template='my_sub_template' alias='/' recurse='items_all'}</strong></pre>
    <p>Similarly, you could have it list only categories under the current page (by omitting the <strong>"alias='/'"</strong>). The combinations are basically limitless!</p>
    <p>All supported values of recurse include:
    <ul>
      <li><strong>items_one</strong> - Catalog items, no more than one level below the current page (or alias point)</li>
      <li><strong>items_all</strong> - Catalog items, at any level below the current page (or alias point)</li>
      <li><strong>categories_one</strong> - Categories, no more than one level below the current page (or alias point)</li>
      <li><strong>categories_all</strong> - Categories, at any level below the current page (or alias point)</li>
      <li><strong>mixed_one</strong> - Catalog items and Categories, no more than one level below the current page (or alias point)</li>
      <li><strong>mixed_all</strong> - Catalog items and Categories, at any level below the current page (or alias point)</li>
    </ul>
    </p>
    <p>Items in these lists come back in hierarchy order. You can change this using the <strong>'global_sort'</strong> parameter. Your choices are:</p>
    <ul>
      <li><strong>alpha</strong> - alphabetically by title</li>
      <li><strong>date</strong> - in order of item creation date</li>
      <li><strong>mdate</strong> - in order of item modification date</li>
    </ul>
    <p>You may order these sorts using the <strong>'global_sort_dir'</strong> parameter, which will allow <strong>'asc'</strong> or <strong>'desc'</strong>.</p>
    <p>So, for a thorough example:</p>
    <pre><strong>&#123;Cataloger action='all' sub_template='my_sub_template' alias='/' recurse='categories_all' global_sort='alpha' global_sort_dir='asc'}</strong></pre> 
    <h4>Recently Added Feature List</h4>
    <p>To use a "most-recently added" list will allow you to display the most recently-added catalog items under a specified part of your hierarchy.</p>
    <p>The syntax for a "most recently added" list is like:</p>
    <pre><strong>&#123;Cataloger action='recent' sub_template='my_sub_template'}</strong></pre> 
    <p>where sub_template is the template to use to render the list. There are four optional parameters, <strong>count='3'</strong>, <strong>alias='page_alias'</strong>, <strong>global_sort='date'</strong>, <strong>global_sort_dir='desc'</strong> where <strong>count</strong> is the number of items to include, <strong>page_alias</strong> indicates the top of the tree (e.g., a place in your menu hierarchy, typically a category page) in which to look for new items. A special value for <strong>"page_alias"</strong> is <strong>"/"</strong>, which means to use <i>all</i> pages in the site. <strong>global_sort</strong> may be <strong>'date'</strong> (for item creation date) or </strong>'mdate'</strong> for modification date, and <strong>global_sort_dir</strong> may be <strong>'asc'</strong> or <strong>'desc'</strong>.</p>
    <h4>Random Items Feature List</h4>
    <p>The syntax for a "random" list is like:</p>
    <pre><strong>&#123;Cataloger action='random' sub_template='my_sub_template'}</strong></pre> 
    <p>where sub_template is the template to use to render the list. There are two optional parameters, <strong>count='3'</strong> and <strong>alias='page_alias'</strong>, where <strong>count</strong> is the number of items to include, and <strong>page_alias</strong> indicates the top of the tree (e.g., a place in your menu hierarchy, typically a category page) in which to look for new items. A special value for <strong>"page_alias"</strong> is <strong>"/"</strong>, which means to use <i>all</i> pages in the site.</p>
  </div>
  {*************************************************************************************}
  {* Image System *}
  <div id="imagesys_c">
    <h3>Cataloger Image System</h3>
    <p>Cataloger has an image system that will allow you to dynamically change the image display size. If you do not like it, you can alter
    the templates and use any image system you want.</p>
    <p>When you upload an image using Cataloger, it gets put in a special set of directories to make the imaging easier. You will see more
    about this later.</p>
    <p>It works by calling a PHP program instead of directly referencing the image. The program uses the filename to see if the
    image already exists in the correct size. If so, it uses that image. Otherwise, it uses your <strong>GDLIB</strong> or <strong>ImageMagick</strong> to create the image in the correct size, and then redirects to it.</p>
    <h4>Cataloger Image Filespec Explanation</h4>
    <p>A cataloger image has a filespec like:</p>
    <pre>&lsaquo;root&rsaquo;/cataloger_image(.page_extention)?i=itemname_f_1_400_1.jpg&amp;ac=82888</pre>
    <p><div class='information'>There are differences in image handling in <strong>Cataloger</strong> since <strong>version 1.0</strong>.<br>The old spec has been removed, and is no longer used,<br> i.e.: <strong>&lsaquo;root_path&rsaquo;/modules/Cataloger/Cataloger.Image.php?i=itemname_f_1_400_1.jpg&ac=82888</strong> spec as explained and used on previous versions <strong>no longer works</strong>.<br>To be more precise, <strong>Cataloger.Image.php</strong> is no longer valid as it used an unsupported method of calling CMSMS core functions. However the filename specs still apply, such as <strong>itemname_f_1_400_1.jpg&ac=82888</strong> along with the <strong>ac</strong> parameter as explained below.</div></p>
    <p><div class='warning'><strong>Cataloger.Image.php</strong> has been replaced by a module action and <strong>Cataloger</strong> will use the new method automatically. No changes in the sub-templates should be needed unless the developer/designer has, for some reason, hard-coded part of the url on the templates instead of using the generated links.</div></p>
    <p>The interesting thing is <strong>"itemname_f_1_400_1.jpg"</strong> - the stuff before is just a call to the program, and the <strong>&amp;ac=82888</strong> is a random thing that is designed to prevent browser caching. So examining the name piece by piece (separating at the underscores), we have:</p>
    <ul>
      <li><strong>item name</strong> - this is the alias of the item/category page containing the image.</li>
      <li><strong>image type</strong> - this is a designation for image type:
        <ul>
          <li><strong>f</strong> - item full-size image</li>
          <li><strong>t</strong> - item thumbnail</li>
          <li><strong>cf</strong> - category full-sized image</li>
          <li><strong>ct</strong> - category thumbnail</li>
          <li><strong>ctf</strong> - printable catalog full-sized image</li>
        </ul>
      </li>
      <li><strong>image number</strong> - so a given item/catalog/etc can have multiple images</li>
      <li><strong>long dimension</strong> - long dimension of the image, in pixels</li>
      <li><strong>missing flag</strong> - 1 if you want to show an "image missing" image if this image doesn't exist, 0 otherwise</li>
    </ul>
    <p>
    This will take an image in <strong>root_path/uploads/images/catalog_src/itemname_src_1</strong>
    <p>As of <strong>version 1.0 Cataloger</strong> has changed the way it handles images. The <strong>Cataloger.Image.php</strong> file as been removed and all image handling is done through a module action used specifically for that purpose.  This allowed better support to pretty URLs, and file obfuscation as the action doesn't redirect to the generated image but generates a copy of the image without revealing the original image location.</p>
    <h4>Accessing your Original Image</h4>
    <p>When Cataloger uploads images, it puts them into a directory called <strong>"cataloger_src"</strong> in your <strong>uploads/images/</strong> directory. The images are named according to the alias of the page: <strong>alias_src_#.jpg</strong> where # is the image number for that page.</p>
    <p>So, to access the originally uploaded images on a Category page, you'd generate the URL manually.</p>
    <p>So on a category page, for example, instead of using {literal}<strong>{$items[numloop].image}</strong>{/literal} you'd use <strong>"/uploads/images/cataloger_src/{literal}{$items[numloop].alias}_src_1.jpg"</strong>{/literal}.</p>
  </div>
  {*************************************************************************************}
  {* Customization and Advanced Topics *}
  <div id="advanced_c">
    <h3>Customization and Advanced Topics</h3>
    <h4>Catalog Item Attributes</h4>
    <p>The default item attributes are typical for a catalog of products or artworks, but by going into <strong><em>Extensions &gt; Cataloger &gt; Manage User-Defined Attributes</em></strong>, you can change the attributes. It's best to define the attributes before you start entering Catalog Items.</p>
    <p><div class='information'>Note that the attributes you define may need to be added to the template that you're using to display Catalog Items:  see Custom Templates below.</div></p>
    <h4>Catalog Category Attributes</h4>
    <p>Similarly, you can set attributes for Categories. As with Items, the attributes you define may need to be added to the template that you're using to display Catalog Categories.</p>
    <h4>Printable Catalog Attributes</h4>
    <p>Likewise, you can set attributes for Printable Catalogs. As with Items and Categories, the attributes you define may need to be added to the template that you're using to display your Printable Catalog.</p>
    <h4>Variable Action</h4>
    <p>You can grab variables (attributes) outside of your Cataloger templates, using the <strong>variable</strong> action parameter. The syntax looks like:</p>
    <pre><strong>&#123;Cataloger action='variable' name='itemnotes' default='no notes'}</strong></pre> 
    <p>where name is the attribute to display (using the same all lower-case, punctuation-free representation like the smarty template variables), and default is the value to use if the attribute is not defined.</p>
    <h4>Item Comparisons</h4>
    <p>As of <strong>version 0.7.7</strong>, there is a <strong>product comparison</strong> action:</p>
    <pre><strong>&#123;Cataloger action='compare' sub_template='my_sub_template' items='item1,item2,item3'}</strong></pre>
    <p>where sub_template is the template to use to render the list, and items is a comma-delimited list of item page aliases to compare. Bolder users may also choose to pass in the items to compare as request variables, thereby enabling dynamic comparisons based on user selections. Do this by setting the <strong>"items"</strong> variable on the request passed to the page containing the Cataloger tag with a compare action.</p>
    <p>The default item comparison template could use some styling, e.g.,:</p>
    <pre>{literal}
    .item_comparison th {font-weight: bold; padding: 5px; text-align:center;}
    .item_comparison td {border: 1px solid gray; padding: 3px 5px;text-align:right;}{/literal}</pre>
  
    <h4>Custom Templates</h4>
    <p>If you're willing to mess around with <strong>Smarty Templates</strong>, you can change the layout of any of the pages generated by Cataloger. There are four kinds of templates: one kind for <strong>Catalog Items</strong>, one kind for <strong>Catalog Categories</strong>, and one kind for <strong>Printable Catalogs</strong>, and one kind for <strong>"Feature"</strong> lists, such as recently added items or random selections.</p>
    <p>When editing a Template, the admin screen will display a list of Smarty tags available to you for that kind of template. This
    will only happen once the module knows what kind of template you're editing, so when you first create the template, it displays all
    the Smarty tags it knows about, only some of which being applicable.</p>
    <h4>Displaying Attributes on Item Pages</h4>
    <p>Attributes are available to Smarty as top-level variables. They are stored as sanitized versions of the attribute names, which means
    it is converted to lower case and all punctuation and non-US-ASCII characters get removed. This is because Smarty is finicky, and will not handle these characters.</p>
    <p>So, for example, if you have an attribute called <strong>"Glücklichkeit"</strong>, there will be a Smarty variable <strong>{$glcklichkeit}</strong>. Note that the "ü" is omitted.</p>
    <p>Fortunately, this is simplified by the $attrlist list available. This is a list of attributes by formal name ("Glücklichkeit") and their key ("glcklichkeit"). For example:
    <pre>{literal}
      {section name=at loop=$attrlist}
      &lt;div class="item_attribute_name">{$attrlist[at].name}:&lt;/div>&lt;div class="item_attribute_val">{eval var=$attrlist[at].key}&lt;/div>
      {/section}{/literal}</pre>
    <p>As of <strong>version 0.8</strong>, you can assign an alias to each attribute. These aliases need to be US-ASCII and contain no punctuation. They can be used instead of the sanitized attribute name (e.g., you could use the alias "gluecklichkeit").</p>
    <h4>Displaying Attributes on Pages with Lists of Items</h4>
    <p>Attributes are included in the <strong>$itemlists[#]</strong> in two different ways: top-level attributes of the item, and as part of the item's <strong>"attr"</strong> array. They are stored as sanitized versions of the attribute names, which means they are converted to lower case and all punctuation and non-US-ASCII characters get removed. This is because Smarty is finicky, and will not handle these characters.</p>
    <p>So, for example, if you have an attribute called <strong>"Glücklichkeit"</strong>, the item will have <strong>$item.glcklichkeit</strong> and <strong>$item.attrs['glcklichkeit']</strong>. Note that the <strong>"ü"</strong> is omitted. You can refer to this attribute in your template
    with Smarty code like: {literal}<strong>{$itemlist[index].glcklickkeit}</strong> or <strong>{$itemlist[index].attrs.glcklichkeit}</strong>{/literal}.</p>
    <p>Fortunately, this is simplified by the <strong>$attrlist</strong> list available. This is a list of attributes by formal name ("Glücklichkeit") and their key ("glcklichkeit"). For example:
    <pre>{literal}
        {foreach from=$attrlist item=attr key=k}
        {$attr}: {$items[numloop][$k]}
        {/foreach}{/literal}</pre>
    <p>As of <strong>version 0.8</strong>, you can assign an alias to each attribute. These aliases need to be <strong>US-ASCII</strong> and contain no punctuation. They can be used instead of the sanitized attribute name (e.g., you could use the alias "gluecklichkeit").</p>
    <h4>Smarty tags in Attributes</h4>
    <p>Sometimes, you'll want the descriptions of your items to include smarty tags (e.g., cms_selflink). To make that work, you'll need to
    update your template. For the attributes where you want smarty tags to be active, you'll need to nest the smarty evaluations in your template. For example:</p>
    <pre>{literal}&#123;eval var=$attrlist[at].key}</pre>
    <p>becomes:</p>{/literal}
    <pre>&#123;eval var=$entry->input assign=thisAttr}&#123;eval var=$thisAttr}</pre>
  </div>
  {*************************************************************************************}
  {* what is new *}
  <div id="whatsnew_c">
    <div class="pageoverflow">
      <h3>What's new</h3>
      <p>This is a summary of the most important changes the module as gone through. For details, please consult the changelog.</p>
      <ul>
        <li>
          Cataloger is now exclusively compatible with CMSMS 2.x;
        </li> 
        <li>
          Cataloger is now compatible PHP 7.x;
        </li>  
        <li>
          Cataloger admin menu has moved to content section;
        </li> 
        <li>
          content types now behave, for the most part, like CMSMS core content types:
          <ul>
            <li>all attribute of the typical content type can be found and used;</li>
            <li>additional content blocks can now be used on page templates - Cataloger will allow it's use;</li>
            <li>cataloger pages can now be previewed by Content Manager - Preview tab;</li>
          </ul>
        </li>
        <li>
          sub templates have a new variable with the main content block giving users some freedom regarding its placement;
        </li> 
        <li>
          image tabs will only be visible if there are images set to be used in the module settings;
        </li>  
        <li>
          file tabs will only be visible if there are files set to be used in the module settings;
        </li>   
        <li>
          there have been some changes to sub-templates although backwards compatibility was kept as much as possible;
        </li>    
        <li>
          the location dir of the images is now concealed if Cataloger native image handling is used;
        </li>    
        <li>
          a huge number of <em>under the hood</em> modifications and optimizations was made to the code;
        </li>    
      </ul>
      <p>Although all efforts have been made to test and make sure the module works without issues, given the number of modifications, minor bugs are expected to exist, so please file a Bug Report if you find one.</p>
      <p><div class='warning'>It is strongly recommended a full backup of files and database before an upgrade. Some, more complex, installations may have issues: most, if not all, of them should be solved by clearing CMSMS cache and/or tweaking sub-templates.</div></p>
    </div> 
  </div>
  {*************************************************************************************}
  {* About / Credits *}
  <div id="about_c">
    
    <h3>Support</h3>
    <div class="pageoverflow">
      <p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
      <ul>
        <li>For the latest version of this module, FAQs, or to file a Bug Report, please visit the CMS Made Simple  <a href="http://dev.cmsmadesimple.org">Developer Forge</a>.</li>
        <li>To obtain commercial support, please send an email to the author at <a href="mailto:jomorg@sm-art-lab.com">&lt;jomorg@sm-art-lab.com&gt;</a>.</li>
        <li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.</li>
        <li>The author can often be found in the <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a>.</li>
        <li>Lastly, you may have some success emailing the author directly and grovelling for free support.</li>
      </ul>
     </div>
       
    <h3>Copyright and License</h3>
    <div class="pageoverflow">    
      <p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>

      <p>Copyright &copy; 2012 - {$smarty.now|date_format:'%Y'}, Fernando Morgado (JoMorg) <a href="mailto:jomorg@sm-art-lab.com">&lt;jomorg@sm-art-lab.com&gt;</a>. All Rights Are Reserved.</p>
      <p>Copyright &copy; 2008, Samuel Goldstein. All Rights Are Reserved.</p>
    </div>
    <div class="pageoverflow">  
      <p style="font-size:85%;font-weight: bold;">This program is free software;<br /> you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;<br /> either version 2 of the License, or (at your option) any later version.</p>
      <p style="font-size:85%;font-weight: bold;">This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;<br /> without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
      <p style="font-size:85%;font-weight: bold;">See theGNU General Public License for more details.</p>
      <p style="font-size:85%;font-weight: bold;">You should have received a copy of the GNU General Public License along with this program;<br /> if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA <a target="_blank" href="http://www.gnu.org/licenses/licenses.html#GPL">or read it online</a>.</p>
      <p style="font-size:85%;font-weight: bold;">You must agree to this license before using the module.</p>
    </div>
    
    <h3>Credits</h3>
    <p>This module has been originaly created, developed and maintained for a number of years by Samuel Goldstein. Fernando Morgado took over and has been maintaining it (for better or for worst) since 2012.</p>
    <p></p>
    <ul>
      <li><strong>Samuel Goldstein (sjg)</strong> - Creation and development;</li>
      <li><strong>Nuno</strong> - code contributions;</li>
      <li><strong>Morten Poulsen (silmarillion)</strong> - code contributions;</li>
      <li><strong>Fernando Morgado (JoMorg)</strong> - development and maintenance;</li>
    </ul>
    
    <h3>Contributors</h3>
    <ul>
      <li><a target="_blank" href="http://www.id-a.co.uk" rel="external">Paul Cooper</a> - sponsored the development of textarea attributes;</li>
      <li><a target="_blank" href="http://www.matthornsby.ca" rel="external">Matt Homsby (DIGI3)</a> - sponsorship;</li>
    </ul>
    <p>Thanks to numerous patient users and bug testers (many of whom had no idea what they were getting into).</p> 
  </div>
</div>


