<form action={concat("content/translations")|ezurl} method="post" >

{section show=$is_edit}
<h1>{"Change translation for content"|i18n("design/standard/content")}</h1>
<p>{"Pick one of the translations from the list to change to or enter a new custom one in the input fields."|i18n("design/standard/content")}</p>
{section-else}
<h1>{"New translation for content"|i18n("design/standard/content")}</h1>
<p>{"Pick one of the translations from the list to add or enter a new custom one in the input fields."|i18n("design/standard/content")}</p>
{/section}

<table cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">

<div class="textblock">
<label>{"Translations"|i18n("design/standard/content")}</label><div class="break"/>
<select name="LocaleID">
  <option value="-1">{"Custom"|i18n("design/standard/content")}</option>
  {section loop=fetch("content","locale_list")}
  <option value="{$:item.locale_full_code|wash}">
  {$:item.intl_language_name|wash}{section show=$:item.country_variation} [{$:item.language_comment|wash}]{/section}
  </option>
  {/section}
</select>
</div>

  </td>

  <td>

<div class="textblock">
<label>{"Name of translation"|i18n("design/standard/content")}</label><div class="break"/>
<input type="edit" name="TranslationName" value=""  size="20" /><br/>
</div>

<div class="textblock">
<label>{"Locale"|i18n("design/standard/content")}</label><div class="break"/>
<input type="text" name="TranslationLocale" value="" size="8" /><br/>
</div>

  </td>
</tr>
</table>

<div class="buttonblock">
{section show=$is_edit}
<input class="defaultbutton" type="submit" name="ChangeButton" value={"Change"|i18n("design/standard/content")} />
{section-else}
<input class="defaultbutton" type="submit" name="StoreButton" value={"Create"|i18n("design/standard/content")} />
{/section}
<input class="button" type="submit" name="CancelButton" value={"Cancel"|i18n("design/standard/content")} />
</div>

</form>
