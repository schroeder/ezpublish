{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div class="toolbox">
    <div class="toolbox-design">
       <div class="toolbox-content">
           <label>{'Search'|i18n( 'design/standard/toolbar' )}</label>
           <form action={"/content/search/"|ezurl} method="get">
           <input class="searchtext" type="text" size="10" name="SearchText" id="Search" value="" />
           <input type="image" src={"t1/t1-button.gif"|ezimage} align="bottom" width="18" height="18" alt="{'Search'|i18n( 'design/standard/toolbar' )}" />
           </form>
       </div>
    </div>
</div>
