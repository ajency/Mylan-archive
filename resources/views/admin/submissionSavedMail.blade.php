
<!-- BEGIN BODY // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
    <tr>
        <td valign="top" class="bodyContent" mc:edit="body_content" style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;text-align: left;">


           

                <span>Hi {{ $user['username'] }}</span>
                <br>
                <br>
                <span>Please review the submission done by your patient from {{ $user['hospitalname'] }} hospital
                </span> 
                <br>
                <br>
                <span>Reference code {{ $user['referencecode'] }}</span>
                <br/>
                 <br/>
                <span>Project name: {{ $user['projectname'] }}</span>
                <br/>
                 <br/> 
                Regards,
                <br/>
                Super admin
         </td>
    </tr>
</table>
