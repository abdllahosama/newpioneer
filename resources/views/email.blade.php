<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title> Notification </title>


    <style>
        @media screen and (min-width: 580px) {
            .padded-sides {
                padding-right: 32px !important;
                padding-left: 32px !important;
            }

            .padded-right {
                padding-right: 32px !important;
            }

            .padded-left {
                padding-left: 32px !important;
            }
        }
    </style>
</head>

<!--header section start-->

<body
    style="
      background-color: #f1f1f1;
      text-align: center;
      font-family: inter, system-ui, sans-serif;
      font-size: 14px;
      padding-right: 8px;
      padding-left: 8px;
      margin: 0;
    ">
    <table align="center" border="0" width="100%"
        style="
        background-color: red-;
        text-align: center;
        padding: 0;
        padding-top: 52px;
        padding-bottom: 52px;
      ">
        <tbody>
            <tr>
                <td style="padding: 0">
                    <table align="center" width="100%" border="0"
                        style="
                border-collapse: collapse;
                background-color: #ffffff;
                max-width: 600px;
                border-bottom: 1px solid #b1cdf6;
              ">
                        <tbody>
                            <tr height="74">
                                <td class="padded-left"
                                    style="
                      padding-left: 16px;
                      padding-top: 22px;
                      padding-bottom: 22px;
                      text-align: left;
                    ">
                                    <a href="#">
                                        <img src="https://exchange.cfundsa.com/_nuxt/logo.4447b4ee.png"
                                            height="24" /></a>
                                </td>
                                <td width="100%"></td>

                                <!-- if there is a client logo add logo here in this commented out line  39   -->
                            </tr>

                            <!--header section end -->

                            <!-- start of white space  with line -->

                            <tr>
                                <td class="padded-sides" style="padding-right: 16px; padding-left: 16px" colspan="3">
                                    <div
                                        style="
                        height: 1px;
                        width: 100%;
                        background-color: #bbd4ff;
                      ">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="padded-sides" style="padding-right: 16px; padding-left: 16px" colspan="3"
                                    height="26"></td>
                            </tr>

                            <!-- start of body  -->

                            <tr>
                                <td class="padded-sides"
                                    style="
                      padding-right: 16px;
                      padding-left: 16px;
                      padding-top: 16px;
                      padding-bottom: 16px;
                      font-size: 62px;
                    "
                                    colspan="3">
                                    {{-- icon of accepted --}}
                                    {{-- 🚚 --}}
                                    <span style="color: green;">&#10003;</span>

                                </td>
                            </tr>


                            <tr>
                                <td class="padded-sides"
                                    style="
                      padding-right: 16px;
                      padding-left: 16px;
                      font-size: 16px;
                      font-weight: 700;
                      color: #006769;
                    "
                                    colspan="3">
                                    {{ $title }}
                                </td>
                            </tr>


                            <tr>
                                <td class="padded-sides"
                                    style="
                      padding-right: 16px;
                      padding-left: 16px;
                      padding-top: 6px;
                      font-size: 14px;
                      color: #000000;
                    "
                                    colspan="3">
                                    {{ $content }}
                                </td>
                            </tr>
                            <tr>
                                <td class="padded-sides"
                                    style="
                      padding-right: 16px;
                      padding-left: 16px;
                      font-size: 14px;
                      color: #000000;
                    "
                                    colspan="3">
                                </td>
                            </tr>



                            <!-- button start -->
                            <tr>
                                <td class="padded-sides"
                                    style="
                      text-align: center;
                      padding-right: 16px;
                      padding-left: 16px;
                      padding-bottom: 16px;
                      padding-top: 16px;
                    "
                                    colspan="3">
                                    <div>
                                        <!--[if mso]>
   <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:37px;v-text-anchor:middle;width:130px;" arcsize="55%" stroke="f" fillcolor="#196DF5">
    <w:anchorlock/>
    <center>
   <![endif]-->

                                        <span height="37"
                                            style="
                          display: inline-block;
                          height: 37px !important;
                          font-family: inter, system-ui, sans-serif;
                          font-size: 14px;
                          vertical-align: top;
                          background-color: #006769;
                          border-radius: 20px;
                          text-align: center;
                        "
                                            valign="top" bgcolor="#196DF5" align="center">
                                            <span width="22px"
                                                style="display: inline-block; width: 22px">&nbsp;</span>
                                            <a height="37"
                                                style="
                            display: inline-block;
                            height: 37px;
                            font-size: 14px;
                            font-weight: 700;
                            color: #ffffff !important;
                            cursor: pointer;
                            text-decoration: none !important;
                            line-height: 37px !important;
                            text-align: center;
                            margin: 0;
                            padding: 0px;
                          "
                                                href="{{ $url }}
                                                "><strong
                                                    style="
                              color: #ffffff !important;
                              line-height: 37px !important;
                            ">للاطلاع</strong></a>
                                            <span width="22px"
                                                style="display: inline-block; width: 22px">&nbsp;</span>
                                        </span>

                                        <!--[if mso]>
    </center>
   </v:roundrect>
   <![endif]-->
                                    </div>
                                </td>
                            </tr>

                            <!-- button end -->

                            <!-- help section start -->

                            <tr>
                                <td colspan="3" class="padded-sides"
                                    style="
                      line-height: 21px;
                      vertical-align: top;
                      padding-right: 16px;
                      padding-left: 16px;
                      padding-bottom: 16px;
                    ">
                                    <a href="https://t.me/mnaaqla" style="text-decoration: none">
                                        <span
                                            style="
                          line-height: 23px;
                          vertical-align: top;
                          font-size: 14px;
                          font-weight: 700;
                          color: #006769;
                        ">تحتاج
                                            الي المساعدة؟</span></a>
                                    🎧
                                </td>
                            </tr>

                            <!-- help section end -->

                            <!-- end of body -->

                            <!--footer section start-->

                            <tr>
                                <td colspan="3" height="26"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="16"></td>
            </tr>

            <tr>
                <td style="padding: 0">
                    <table align="center" width="100%" border="0"
                        style="
                border-collapse: collapse;
                background-color: #ffffff;
                max-width: 600px;
              ">
                        <tbody style="padding-top: 32px; padding-bottom: 32px">
                            <tr>
                                <td class="padded-sides"
                                    style="
                      padding-right: 16px;
                      padding-left: 16px;
                      padding-top: 32px;
                      padding-bottom: 20px;
                    ">
                                    <a href="#">
                                        <img src="https://exchange.cfundsa.com/_nuxt/logo.4447b4ee.png" width="auto"
                                            height="40" /></a>
                                </td>
                            </tr>
                            <tr>
                                <td class="padded-sides"
                                    style="
                      line-height: 1.6;
                      padding-right: 16px;
                      padding-left: 16px;
                      font-size: 12px;
                      padding-bottom: 32px;
                    ">
                                    <a style="
                        display: inline-block;
                        color: #ffffff !important;
                        text-decoration: none;
                        white-space: nowrap;
                      "
                                        href="https://t.me/mnaaqla"><strong
                                            style="
                          font-weight: 400 !important;
                          color: #006769 !important;
                        ">@mnaaqla</strong></a>
                                    <a
                                        style="
                        display: inline-block;
                        color: #ffffff !important;
                        text-decoration: none;
                        padding-left: 10px;
                        white-space: nowrap;
                      "><strong
                                            style="
                          font-weight: 400 !important;
                          color: #006769 !important;
                        ">Riyadh,
                                            Saudi Arabia</strong></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>

<!--footer section end-->

<!-- end of code -->
