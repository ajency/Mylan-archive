
<!-- BEGIN BODY // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
    <tr>
        <td valign="top" class="bodyContent" mc:edit="body_content" style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;text-align: left;">


           

                <span>Sorry {{ $user['name'] }} you've been having trouble logging into your mylan account.</span>
                <br>
                <br>
                <span>On your request, password for your account registered with email {{ $user['email'] }} has been reset to: {{ $user['password']}}
                </span> 
                <br>
                <br>

                Please log in to the link below with the updated password <br>
                 <?php echo $user['loginUrls'] ?> 
                <br/>
                 <br/>
                Thanks,
                <br/>
                Mylan
         </td>
    </tr>
</table>
