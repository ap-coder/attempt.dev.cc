<?php get_header('members');?>
<?php 

?>
<?php include('partial/sidebar.php'); ?>

<div id="main" class="none">
  
  <div class="page-content">
    
    <div class="col-sm-12 middle-align">
      
      <h4>Client Information</h4>
      <div class="well">
        <form action="<?php echo secure_route('account/updateprofile');?>" method="POST" class="ajax-save">        
          
          <div class="row">
            <div class="col-sm-6"><p><label for="full_name">Name*</label><br /><input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo $data['profile_info']['first_name'] . ' ' . $data['profile_info']['last_name'];?>"></p></div>
            <div class="col-sm-6"><p><label for="company">Company*</label><br /><input type="text" id="company" name="company" class="form-control" value="<?php echo $data['profile_info']['company']?>"></p></div>
            <div class="col-sm-6"><p><label for="address_street">Street Address*</label><br /><input type="text" id="address_street" name="address_street" class="form-control" value="<?php echo $data['profile_info']['address_street']?>"></p></div>
            <div class="col-sm-6"><p><label for="address_city">City*</label><br /><input type="text" id="address_city" name="address_city" class="form-control" value="<?php echo $data['profile_info']['address_city']?>"></p></div>
            <div class="col-sm-6"><p><label for="address_state">State*</label><br />
              
                  <select data-stripe="address_state" id="address_state" class="form-control">
                    <option value="">-- Select State --</option>
                    <?php foreach(fifty_states() as $state => $abbr):?>
                    <option value="<?php echo $abbr;?>" <?php if($data['profile_info']['address_state'] == $abbr) {echo 'selected="selected"';}?>><?php echo $state; ?></option>
                    <?php endforeach;?>
                  </select>

            </p></div>
            <div class="col-sm-6"><p><label for="address_zip">Zip*</label><br /><input type="text" id="address_zip" name="address_zip" class="form-control" value="<?php echo $data['profile_info']['address_zip']?>"></p></div>
            <div class="col-sm-6"><p><label for="phone">Phone</label><br /><input type="text" id="phone" name="phone" class="form-control" value="<?php echo $data['profile_info']['phone']?>"></p></div>
            <div class="col-sm-6"><p><label for="email">Email*</label><br /><input type="text" id="email" name="email" class="form-control" value="<?php echo $data['profile_info']['email']?>"></p></div>

            <div class="col-sm-12"> <button class="right">Save</button> <div class="message-box"></div> </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-sm-12">
      <h4>Credit Card Information</h4>
      <div class="well">
        
        <div class="row">
          <div class="col-sm-12">
            <div class="left"><strong style=" top: 3px; color: #565656;">Current Card: &nbsp; &nbsp;</strong> </div>
            <div class="spacey none">
              <?php echo isset($data['card']) && isset($data['card']->last4) ? '<div class="white-label">****-****-****-'.$data['card']->last4 . '</div> <div class="white-label">' . $data['card']->exp_month . '/' .$data['card']->exp_year.'</div>': '<div class="white-label">No card on file.</div>'; ?>
            </div>
          </div>
        </div>

        <br />

        <form id="updatecard-form" action="<?php echo route('account/updatecard')?>" method="POST">
          
        <div class="row">

          <div class="col-sm-12">
            <p><label>Card Number*</label> <br />
              <input type="text" data-stripe="number" value="" id="#card-number">
            </p>
          </div>

          <div class="col-sm-12">
            <p style="margin-bottom: 0"><label>Month/Year/CVV*</label></p>
            <div class="row">
              <div class="col-sm-4">
                <p><select data-stripe="exp_month" class="form-control">
                  <option value="">-- MM --</option>
                  <?php for($x=1; $x<13; $x++):?>
                    <option value="<?php echo $x;?>"><?php echo $x;?></option>
                  <?php endfor; ?>
                </select></p>
              </div>
              <div class="col-sm-4">
                <p><select data-stripe="exp_year" class="form-control">
                  <option value="">-- YY --</option>
                  <?php for( $x=date('Y'); $x<date('Y')+10; $x++ ) : ?>
                    <option value="<?php echo $x;?>"><?php echo $x;?></option>
                  <?php endfor; ?>
                </select></p>
              </div>
              <div class="col-sm-4">
                <p><input type="text" data-stripe="cvc" value="" placeholder="CVV"></p>
              </div>
            </div>
          </div>

          <div class="col-sm-12">

            <input class="submit right" type="submit" value="Update" ><div class="message-box"></div>
          </div>
        </div>

        </form>

        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script>

          Stripe.setPublishableKey('<?php echo STRIPE_PUB; ?>');

          function stripeResponseHandler(status, response) {

            // Grab the form:
            var $form = jQuery('#updatecard-form');
            
            if (response.error) { // Problem!

              // Show the errors on the form:
              $form.find('.message-box').html('<div class="alert alert-danger">'+response.error.message+'</div>');

              $form.find('.submit').prop('disabled', false); // Re-enable submission

            } else { // Token was created!

              // Get the token ID:
              var token = response.id;

              // Insert the token ID into the form so it gets submitted to the server:
              $form.append(jQuery('<input type="hidden" name="stripeToken">').val(token));
              
              convertToDefault($form);

              // Submit the form:
              $form.get(0).submit();
            }

          };

          jQuery(document).ready(function($){

            var $form = $('#updatecard-form');
            
            convertToDefault($form);

            $form.submit(function(event) {
              
              event.preventDefault();

              var el = $(event.target);

              $.ajax({
                url : '<?php echo route('account/validate');?>',
                type: 'POST',
                data : $form.serialize(),
                dataType: 'json',
                beforeSend: function(d){
                  $('p.error').remove();
                },
                success: function(d){

                  if( d.status == 'fail'){

                    for( var prop in d.error ){
                    
                      if( d.error.hasOwnProperty( prop ) ) {
                    
                        $('[name="'+prop+'"]').after('<p class="error small">'+d.error[prop]+'</p>');
                      } 
                    }

                  } else {
                    
                    convertToStripe($form);
                    
                    $form.find('.payment-errors').html('');

                    // Disable the submit button to prevent repeated clicks:
                    $form.find('.submit').removeClass('inactive').prop('disabled', true);

                    // Request a token from Stripe:
                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from being submitted:
                    return false;

                  }
                }
              });

            });

          });

        </script>
      </div>
    </div>
    
    <div class="col-sm-6">
        <h4>Change Password</h4>
        <div class="well">
          <form action="<?php echo secure_route('auth/password')?>" method="POST" data-validate="<?php echo route('auth/validate')?>" class="ajax-save">
            <input type="hidden" name="email" value="<?php echo $data['user']->email; ?>">
            <input type="hidden" name="user_id" value="<?php echo $data['user']->ID; ?>">
            <input type="hidden" name="action" value="password">
            <table>
              <tr>
                <td style="width:150px" >Current Password</td>
                <td><input type="password" name="current_password"></td>
              </tr>
              <tr>
                <td>Password</td>
                <td><input type="password" name="password"></td>
              </tr>
              <tr>
                <td>Confirm Password</td>
                <td><input type="password" name="password2"></td>
              </tr>
              <tr>
                <td>          
                </td>
                <td>
                  <?php if( isset($data['message']) ):?>
                    <div class="message-box"><div class="alert alert-success"> <?php echo $data['message'];?> </div></div>
                  <?php endif;?>
                  <input type="submit" class="right" value="Save Changes">
                </td>
              </tr>
            </table>

          </form>
        </div>
    </div>

    <div class="col-sm-6">
      <h4>Cancel Subscription</h4>
      <div class="well">
        <?php if( $subscription = get_user_meta( get_current_user_id(), 'subscription_id', true ) ):?>
          <p>If you need lower payments you can always <a href="<?php echo site_url( '/pricing')?>">Downgrade Your Support Plan</a>.</p>
          <a href="<?php echo route('account/retain')?>" class="btn btn-danger">Cancel Subscription</a>
        <?php else: ?>
          <p>You are not enrolled in any Support Plans at this time. <a href="<?php echo site_url( '/pricing');?>">Choose a plan.</a></p>
          <p style="text-align: center;"><a href="<?php echo site_url( '/pricing');?>"><img src="<?php echo site_url( '/wp-content/uploads/2018/01/support-plans-300.jpg' )?>" alt=""></a></p>
        <?php endif;?>
      </div>
    </div>

  </div>

</div>

<?php get_footer(); ?>