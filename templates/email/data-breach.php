
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title><?php esc_html_e( 'Data Breach Notification Confirmation', 'gdpr' ); ?></title>
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
            <h3><?php esc_html_e( 'Data Breach Notification Confirmation.', 'gdpr' ); ?></h3>
            <p><?php esc_html_e( 'Someone requested to notify users of a data breach event.', 'gdpr' ); ?></p>
            <h4><?php esc_html_e( 'Nature of the personal data breach', 'gdpr' ) ?></h4>
            <p><?php echo wp_kses_post( $args['nature'] ); ?></p>
            <h4><?php esc_html_e( 'Name and contact details of the data protection officer', 'gdpr' ) ?></h4>
            <p><?php echo wp_kses_post( $args['contact'] ); ?></p>
            <h4><?php esc_html_e( 'Likely consequences of the personal data breach', 'gdpr' ) ?></h4>
            <p><?php echo wp_kses_post( $args['consequences'] ); ?></p>
            <h4><?php esc_html_e( 'Measures taken or proposed to be taken', 'gdpr' ) ?></h4>
            <p><?php echo wp_kses_post( $args['measures'] ); ?></p>
            <p align="center" class="button"><a href="<?php echo esc_url( admin_url( 'admin.php?page=gdpr-data-breach&action=data-breach&key=' . $args['key'] ) ); ?>"><?php esc_html_e( 'Confirm', 'gdpr' ) ?></a></p>
            <p>
              <?php
                echo sprintf(
                  __( 'If that wasn\'t you, <a href="%s">Reset your password</a>', 'gdpr' ),
                  esc_url( wp_login_url() . '?action=rp&key=' . get_password_reset_key( $args['user'] ) . '&login=' . $args['user']->user_login )
                );
              ?>
            </p>
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
