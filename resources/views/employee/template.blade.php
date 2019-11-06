<style type="text/css">
    /*////// RESET STYLES //////*/
    body,
    #bodyTable,
    #bodyCell {
        height: 100% !important;
        margin: 0;
        padding: 0;
        width: 100% !important;
    }
    table {
        border-collapse: collapse;
    }

    img,
    a img {
        border: 0;
        outline: none;
        text-decoration: none;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin: 0;
        padding: 0;
    }

    p {
        margin: 1em 0;
    }

    /*////// CLIENT-SPECIFIC STYLES //////*/
    .ReadMsgBody {
        width: 100%;
    }

    .ExternalClass {
        width: 100%;
    }

    /* Force Hotmail/Outlook.com to display emails at full width. */
    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
        line-height: 100%;
    }

    /* Force Hotmail/Outlook.com to display line heights normally. */
    table,
    td {
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }

    /* Remove spacing between tables in Outlook 2007 and up. */
    #outlook a {
        padding: 0;
    }

    /* Force Outlook 2007 and up to provide a "view in browser" message. */
    img {
        -ms-interpolation-mode: bicubic;
    }

    /* Force IE to smoothly render resized images. */
    body,
    table,
    td,
    p,
    a,
    li,
    blockquote {
        -ms-text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }

    /* Prevent Windows- and Webkit-based mobile platforms from changing declared text sizes. */

    /*////// FRAMEWORK STYLES //////*/
    .flexibleContainerCell {
        padding-top: 20px;
        padding-Right: 20px;
        padding-Left: 20px;
    }

    .flexibleImage {
        height: auto;
    }

    .bottomShim {
        padding-bottom: 20px;
    }

    .imageContent,
    .imageContentLast {
        padding-bottom: 20px;
    }

    .nestedContainerCell {
        padding-top: 20px;
        padding-Right: 20px;
        padding-Left: 20px;
    }

    /*////// GENERAL STYLES //////*/
    body,
    #bodyTable {
        background-color: #F5F5F5;
    }

    #bodyCell {
        padding-top: 40px;
        padding-bottom: 40px;
    }

    #emailBody {
        background-color: #FFFFFF;
        border: 1px solid #DDDDDD;
        border-collapse: separate;
        border-radius: 4px;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        color: #202020;
        font-family: Helvetica;
        font-size: 20px;
        line-height: 125%;
        text-align: Left;
    }

    .textContent,
    .textContentLast {
        color: #404040;
        font-family: Helvetica;
        font-size: 16px;
        line-height: 125%;
        text-align: Left;
        padding-bottom: 20px;
    }

    .textContent a,
    .textContentLast a {
        color: #2C9AB7;
        text-decoration: underline;
    }

    .nestedContainer {
        background-color: #E5E5E5;
        border: 1px solid #CCCCCC;
    }

    .emailButton {
        background-color: #2C9AB7;
        border-collapse: separate;
        border-radius: 4px;
    }

    .buttonContent {
        color: #FFFFFF;
        font-family: Helvetica;
        font-size: 18px;
        font-weight: bold;
        line-height: 100%;
        padding: 15px;
        text-align: center;
    }

    .buttonContent a {
        color: #FFFFFF;
        display: block;
        text-decoration: none;
    }

   

    /*////// MOBILE STYLES //////*/
    @media only screen and (max-width: 480px) {

        /*////// CLIENT-SPECIFIC STYLES //////*/
        body {
            width: 100% !important;
            min-width: 100% !important;
        }

        table[id="emailBody"],
        table[class="flexibleContainer"] {
            width: 100% !important;
        }

        table[class="emailButton"] {
            width: 100% !important;
        }

        td[class="buttonContent"] {
            padding: 0 !important;
        }

        td[class="buttonContent"] a {
            padding: 15px !important;
        }

        td[class="textContentLast"],
        td[class="imageContentLast"] {
            padding-top: 20px !important;
        }

        /*////// GENERAL STYLES //////*/
        td[id="bodyCell"] {
            padding-top: 10px !important;
            padding-Right: 10px !important;
            padding-Left: 10px !important;
        }
    }
</style>


<body>
    <center>
        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
            <tr>
                <td align="center" valign="top" id="bodyCell">

                    <table border="0" cellpadding="0" cellspacing="0" width="600" id="emailBody">

                        <!-- MODULE ROW // -->
                        <tr>
                            <td align="center" valign="top">
                                <!-- CENTERING TABLE // -->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td align="center" valign="top">
                                            <!-- FLEXIBLE CONTAINER // -->
                                            <table border="0" cellpadding="0" cellspacing="0" width="600"
                                                class="flexibleContainer">
                                                <tr>
                                                    <td align="center" valign="top" width="600"
                                                        class="flexibleContainerCell bottomShim">
                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                                            class="nestedContainer">
                                                            <tr>
                                                                <td align="center" valign="top"
                                                                    class="nestedContainerCell">


                                                                    <!-- CONTENT TABLE // -->
                                                                    <table border="0" cellpadding="0" cellspacing="0"
                                                                        width="100%">
                                                                        <tr>
                                                                            <td valign="top" class="textContent">
                                                                                <h3>Dear {{$name}},</h3><br>
                                                                                Your new  Law 5  account has been created, Welcome to the Law 5 community.
                                                                                From now, please log in to your account using:
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td align="center" valign="top"
                                                                                class="bottomShim">
                                                                                <table border="0" cellpadding="0"
                                                                                    cellspacing="0" width="460"
                                                                                    class="emailButton">
                                                                                    <tr>
                                                                                        <td align="center"
                                                                                            class="buttonContent">
                                                                                            Email: {{$email}} <br><br>
                                                                                            Password: {{$password}}
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="bottom" class="textContent">
                                                                            If you received this email but you didn't register please ignore it.
                                                                            This is an automated message please don't reply.
                                                                            <br>
                                                                                <h3>Thanks, </h3>
                                                                                Law 5 Team
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                            <!-- // FLEXIBLE CONTAINER -->
                                        </td>
                                    </tr>
                                </table>
                                <!-- // CENTERING TABLE -->
                            </td>
                        </tr>
                        <!-- // MODULE ROW -->

                    </table>
                    <!-- // EMAIL CONTAINER -->
                </td>
            </tr>
        </table>
    </center>
</body>