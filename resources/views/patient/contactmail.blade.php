
<!-- BEGIN BODY // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
    <tr>
        <td valign="top" class="bodyContent" mc:edit="body_content" style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;text-align: left;">


           Dear {{ $data['hospital_name']}},
            <br/>
            <h3>Inquiry from {{ $data['patient_name']}}</h3>

                 
             <br>
                <span>Name:</span> {{ $data['patient_name']}}<br> 
                <span>Email:</span> {{ $data['patient_email']}}<br>
                <span>Phone:</span> {{ $data['patient_phone']}}<br>
                <span>Reference Code:</span> {{ $data['patient_reference_code']}}<br>
                <span>Project:</span> {{ $data['project']}}<br> 
                <span>Message:</span> {{ $data['message']}}<br> 
                <br>
                <br>
                
    <br/>
                Thanks,
                <br/>
                Mylan
         </td>
    </tr>
</table>
