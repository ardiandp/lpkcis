<?php
/**
 * Cart totals
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<div class="span6 cart_totals <?php if ( WC()->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>">
    <div class="border-1 border">
        <div class="border-2 border">

            <?php do_action( 'woocommerce_before_cart_totals' ); ?>

            <h2><?php _e( 'Cart Totals', 'yit' ); ?></h2>

            <table align="right" cellspacing="0" cellpadding="0">

                <tr class="cart-subtotal">
                    <th><?php _e( 'Cart Subtotal', 'yit' ); ?></th>
                    <td><strong><?php wc_cart_totals_subtotal_html(); ?></strong></td>
                </tr>

                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                    <tr class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
                        <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                        <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

                    <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

                    <?php wc_cart_totals_shipping_html(); ?>

                    <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

                <?php endif; ?>

                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <tr class="fee fee-<?php echo $fee->id ?>">
                        <th><?php echo esc_html( $fee->name ); ?></th>
                        <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if ( WC()->cart->get_tax_price_display_mode() == 'excl' ) : ?>
                    <?php if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) : ?>
                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                            <tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
                                <th><?php echo esc_html( $tax->label ); ?></th>
                                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="tax-total">
                            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                            <td><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

                <tr class="total">
                    <th><?php _e( 'Order Total', 'yit' ); ?></th>
                    <td><strong><?php  wc_cart_totals_order_total_html() ?></strong></td>
                </tr>

                <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

            </table>

            <?php if ( WC()->cart->get_cart_tax() ) : ?>
                <p>
                    <small><?php
                        $estimated_text = ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'yit' ), WC()->countries->estimated_for_prefix() . __( WC()->countries->countries[WC()->countries->get_base_country()], 'yit' ) ) : '';
                        printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'yit' ), $estimated_text );
                        ?>
                    </small>
                </p>
            <?php endif; ?>

<!--            <div class="wc-proceed-to-checkout">-->
<!---->
<!--                --><?php //do_action( 'woocommerce_proceed_to_checkout' ); ?>
<!---->
<!--            </div>-->

            <?php do_action( 'woocommerce_after_cart_totals' ); ?>

            <div class="clear"></div>
        </div>
    </div>
</div>