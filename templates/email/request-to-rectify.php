
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title><?php esc_html( $args['email_title'] ) ?></title>
    <style>
      body {
        background: #d5d6d7;
      }
      #body{
        background: #fff;
        width: 100%;
        max-width: 760px;
        margin: 0 auto;
        font-family: 'Lucida Grande',Verdana,Arial,Sans-Serif;
      }
      .spacer{
        height: 20px;
        width: 100%;
      }
      th {
        background: #73a0c5;
        color: #fff;
        text-align: center;
      }
      tbody td, tfoot td {
        padding: 0 30px;
      }
      .button{
        margin: 40px 0;
      }
      .button a{
        text-decoration: none;
        background: #00b52a;
        color: #fff;
        padding: 7px 40px;
      }
      a{
        color: #5695c9;
      }
    </style>
  </head>
  <body>
    <table id="body">
      <thead>
        <tr>
        <th><h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1></th>
        </tr>
      </thead>
      <tr class="spacer"></tr>
      <tbody>
        <tr>
          <td>
            <h4>Someone placed a request for your information to be rectified on our site.</h4>
            <p>By clicking confirm a request will be made and we will do our best to fulfil it.</p>
            <p><strong>This is the request</strong></p>
            <blockquote>
              <?php echo wp_kses( wpautop( $args['data'] ), array( 'p' => true, 'br' => true ) ); ?>
            </blockquote>
            <?php
              $confirm_url = add_query_arg(
                array(
                  'action' => 'add-to-rectify',
                  'key' => $args['key'],
                  'email' => $args['user']->user_email,
                  'data' => $args['data'],
                ),
                home_url()
              );
              $forgot_password_url = add_query_arg(
                array(
                  'action' => 'rp',
                  'key' => get_password_reset_key( $args['user'] ),
                  'login' => $args['user']->user_login,
                ),
                wp_login_url()
              );
            ?>
            <p align="center" class="button"><a href="<?php echo esc_url( $confirm_url ); ?>">Confirm</a></p>
            <p>If that wasn't you, <a href="<?php echo esc_url( $forgot_password_url ); ?>">reset your password</a>.</p>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td>
          </td>
        </tr>
      </tfoot>
      <tr class="spacer"></tr>
    </table>

    <!-- prevent Gmail on iOS font size manipulation -->
   <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
  </body>
</html>
