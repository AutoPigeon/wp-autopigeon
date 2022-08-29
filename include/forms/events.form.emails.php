<?php

require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/forms/form.php");
require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/emails.php");

class AP_Events_Emails_Form extends AP_Form{
    function build(){
        ?>
        <h1>
            Please Select The Email That Will Be Sent
        </h1>
        <table>
            <tbody class="form-table">
                <tr class="form-field form-required">
                    <th scope="row">
                        <label for="name">
                            Select Event Name
                        </label>
                    </th>
                    <td>
                
                        <input type="text" name="name" id="name">
                        <?php echo $this->page->get_form_field_errors_as_html("name"); ?>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th scope="row">
                        <label for="email">
                            Select an Email
                        </label>
                    </th>
                    <td>
                
                        <select  name="email" id="email" size="1" >
                            <option value="0" >  --- SELECT AN EMAIL --- </option>
                            <?php
                                $emails = AP_Emails::get_emails();
                                foreach ($emails as $email){
                                    ?> 
                                    <option value='<?php echo esc_html($email["id"]); ?>'>
                                        <?php echo esc_html($email["subject"]); ?>
                                    </option><?
                                }
                            ?>
                        </select>
                        <?php echo $this->page->get_form_field_errors_as_html("email"); ?>
                    </td>
                </tr>
                
            </tbody>
        </table>
             <p class="submit">
                <input type="submit" name="submit" class="button button-primary" value="Continue">
            </p>
        <?php
    }
}


?>