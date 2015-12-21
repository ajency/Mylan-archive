
<!-- BEGIN BODY // -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateBody">
    <tr>
        <td valign="top" class="bodyContent" mc:edit="body_content" style="color: #505050;font-family: Arial;font-size: 14px;line-height: 150%;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;text-align: left;">


           Dear {{ $user['name']}},
            <br/>
            <h3>Welcome to Mylan!</h3>

                Your Account on Mylan has been created with the following credentials -
             <br>
                <br>
                <span>Email:</span> {{ $user['email']}}<br>
                <span>Your account has been set with a randomly generated password :</span> <span class="bold">{{ $user['password']}}</h6></span>
                <br>
                <br>

                To go back to the Login page, <a href="{{  url() }}/admin">click here</a>

                 or copy paste the link below in your browser to login to your account:<br>
                <a href="{{  url() }}/admin">{{  url() }}/admin</a>

                <br/>

 
                 
    <br/>
                Thanks,
                <br/>
                Mylan
         </td>
    </tr>
</table>
