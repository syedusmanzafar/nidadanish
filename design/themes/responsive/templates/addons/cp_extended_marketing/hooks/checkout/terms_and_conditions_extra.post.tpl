{if $addons.cp_extended_marketing.ask_about_reviews == "Y"}
    <div class="ty-control-group ty-checkout__terms">
        <div class="cm-field-container">
            <label for="cp_em_ask_about_reviews" class="checkbox">
            <input type="hidden" name="ask_about_reviews" value="N" />
            <input type="checkbox" id="cp_em_ask_about_reviews" name="ask_about_reviews" value="Y" class="cm-agreement checkbox" checked="checked"><span>{__("cp_em_ask_review_txt")}</span></label>
        </div>
    </div>
{/if}