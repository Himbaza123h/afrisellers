<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manageable_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ──────────────────────────────────────────
            // GROUP : dashboard
            // ──────────────────────────────────────────
            $table->string('group')->default('dashboard')->comment('Permission group label e.g. vendors, orders, escrow');
            $table->boolean('can_view_dashboard')->default(false);

            // ──────────────────────────────────────────
            // GROUP : vendors
            // ──────────────────────────────────────────
            $table->boolean('can_view_vendors')->default(false);
            $table->boolean('can_create_vendors')->default(false);
            $table->boolean('can_edit_vendors')->default(false);
            $table->boolean('can_verify_vendors')->default(false);

            // ──────────────────────────────────────────
            // GROUP : regional_admins
            // ──────────────────────────────────────────
            $table->boolean('can_view_regional_admins')->default(false);
            $table->boolean('can_create_regional_admins')->default(false);
            $table->boolean('can_edit_regional_admins')->default(false);
            $table->boolean('can_delete_regional_admins')->default(false);
            $table->boolean('can_activate_regional_admins')->default(false);
            $table->boolean('can_deactivate_regional_admins')->default(false);
            $table->boolean('can_switch_to_regional_admin')->default(false);
            $table->boolean('can_assign_regional_admin_user')->default(false);
            $table->boolean('can_print_regional_admins')->default(false);

            // ──────────────────────────────────────────
            // GROUP : countries
            // ──────────────────────────────────────────
            $table->boolean('can_view_countries')->default(false);
            $table->boolean('can_create_countries')->default(false);
            $table->boolean('can_edit_countries')->default(false);
            $table->boolean('can_delete_countries')->default(false);
            $table->boolean('can_activate_countries')->default(false);
            $table->boolean('can_deactivate_countries')->default(false);
            $table->boolean('can_switch_to_country')->default(false);
            $table->boolean('can_assign_country_admin')->default(false);
            $table->boolean('can_toggle_country_status')->default(false);
            $table->boolean('can_print_countries')->default(false);

            // ──────────────────────────────────────────
            // GROUP : business_profiles
            // ──────────────────────────────────────────
            $table->boolean('can_view_business_profiles')->default(false);
            $table->boolean('can_verify_business_profiles')->default(false);
            $table->boolean('can_reject_business_profiles')->default(false);
            $table->boolean('can_suspend_business_profiles')->default(false);
            $table->boolean('can_activate_business_profiles')->default(false);
            $table->boolean('can_switch_to_vendor')->default(false);
            $table->boolean('can_print_business_profiles')->default(false);

            // ──────────────────────────────────────────
            // GROUP : buyers
            // ──────────────────────────────────────────
            $table->boolean('can_view_buyers')->default(false);
            $table->boolean('can_create_buyers')->default(false);
            $table->boolean('can_edit_buyers')->default(false);
            $table->boolean('can_delete_buyers')->default(false);
            $table->boolean('can_suspend_buyers')->default(false);
            $table->boolean('can_activate_buyers')->default(false);
            $table->boolean('can_switch_to_buyer')->default(false);
            $table->boolean('can_update_buyer_status')->default(false);
            $table->boolean('can_print_buyers')->default(false);

            // ──────────────────────────────────────────
            // GROUP : agents
            // ──────────────────────────────────────────
            $table->boolean('can_view_agents')->default(false);
            $table->boolean('can_create_agents')->default(false);
            $table->boolean('can_edit_agents')->default(false);
            $table->boolean('can_delete_agents')->default(false);
            $table->boolean('can_verify_agents')->default(false);
            $table->boolean('can_suspend_agents')->default(false);
            $table->boolean('can_switch_to_agent')->default(false);
            $table->boolean('can_print_agents')->default(false);

            // ──────────────────────────────────────────
            // GROUP : transporters
            // ──────────────────────────────────────────
            $table->boolean('can_view_transporters')->default(false);
            $table->boolean('can_create_transporters')->default(false);
            $table->boolean('can_edit_transporters')->default(false);
            $table->boolean('can_delete_transporters')->default(false);
            $table->boolean('can_verify_transporters')->default(false);
            $table->boolean('can_suspend_transporters')->default(false);
            $table->boolean('can_print_transporters')->default(false);

            // ──────────────────────────────────────────
            // GROUP : product_categories
            // ──────────────────────────────────────────
            $table->boolean('can_view_product_categories')->default(false);
            $table->boolean('can_create_product_categories')->default(false);
            $table->boolean('can_edit_product_categories')->default(false);
            $table->boolean('can_delete_product_categories')->default(false);
            $table->boolean('can_activate_product_categories')->default(false);
            $table->boolean('can_toggle_product_category_status')->default(false);
            $table->boolean('can_print_product_categories')->default(false);

            // ──────────────────────────────────────────
            // GROUP : products
            // ──────────────────────────────────────────
            $table->boolean('can_view_products')->default(false);
            $table->boolean('can_create_products')->default(false);
            $table->boolean('can_edit_products')->default(false);
            $table->boolean('can_delete_products')->default(false);
            $table->boolean('can_approve_products')->default(false);
            $table->boolean('can_reject_products')->default(false);
            $table->boolean('can_feature_products')->default(false);
            $table->boolean('can_toggle_product_verification')->default(false);
            $table->boolean('can_print_products')->default(false);

            // ──────────────────────────────────────────
            // GROUP : vendor_products
            // ──────────────────────────────────────────
            $table->boolean('can_view_vendor_products')->default(false);
            $table->boolean('can_create_vendor_products')->default(false);
            $table->boolean('can_edit_vendor_products')->default(false);
            $table->boolean('can_delete_vendor_products')->default(false);
            $table->boolean('can_approve_vendor_products')->default(false);
            $table->boolean('can_reject_vendor_products')->default(false);
            $table->boolean('can_bulk_delete_vendor_products')->default(false);
            $table->boolean('can_bulk_status_vendor_products')->default(false);
            $table->boolean('can_bulk_upload_vendor_products')->default(false);
            $table->boolean('can_manage_vendor_product_images')->default(false);
            $table->boolean('can_download_vendor_product_template')->default(false);
            $table->boolean('can_print_vendor_products')->default(false);

            // ──────────────────────────────────────────
            // GROUP : orders
            // ──────────────────────────────────────────
            $table->boolean('can_view_orders')->default(false);
            $table->boolean('can_create_orders')->default(false);
            $table->boolean('can_edit_orders')->default(false);
            $table->boolean('can_cancel_orders')->default(false);
            $table->boolean('can_accept_orders')->default(false);
            $table->boolean('can_process_orders')->default(false);
            $table->boolean('can_ship_orders')->default(false);
            $table->boolean('can_complete_orders')->default(false);
            $table->boolean('can_refund_orders')->default(false);
            $table->boolean('can_view_order_invoice')->default(false);
            $table->boolean('can_download_order_invoice')->default(false);
            $table->boolean('can_print_orders')->default(false);

            // ──────────────────────────────────────────
            // GROUP : rfqs
            // ──────────────────────────────────────────
            $table->boolean('can_view_rfqs')->default(false);
            $table->boolean('can_create_rfqs')->default(false);
            $table->boolean('can_edit_rfqs')->default(false);
            $table->boolean('can_delete_rfqs')->default(false);
            $table->boolean('can_approve_rfqs')->default(false);
            $table->boolean('can_reject_rfqs')->default(false);
            $table->boolean('can_view_rfq_vendors')->default(false);
            $table->boolean('can_view_rfq_messages')->default(false);
            $table->boolean('can_send_rfq_messages')->default(false);
            $table->boolean('can_print_rfqs')->default(false);

            // ──────────────────────────────────────────
            // GROUP : showrooms
            // ──────────────────────────────────────────
            $table->boolean('can_view_showrooms')->default(false);
            $table->boolean('can_verify_showrooms')->default(false);
            $table->boolean('can_unverify_showrooms')->default(false);
            $table->boolean('can_feature_showrooms')->default(false);
            $table->boolean('can_activate_showrooms')->default(false);
            $table->boolean('can_suspend_showrooms')->default(false);
            $table->boolean('can_delete_showrooms')->default(false);
            $table->boolean('can_print_showrooms')->default(false);

            // ──────────────────────────────────────────
            // GROUP : tradeshows
            // ──────────────────────────────────────────
            $table->boolean('can_view_tradeshows')->default(false);
            $table->boolean('can_approve_tradeshows')->default(false);
            $table->boolean('can_verify_tradeshows')->default(false);
            $table->boolean('can_unverify_tradeshows')->default(false);
            $table->boolean('can_feature_tradeshows')->default(false);
            $table->boolean('can_suspend_tradeshows')->default(false);
            $table->boolean('can_delete_tradeshows')->default(false);
            $table->boolean('can_print_tradeshows')->default(false);

            // ──────────────────────────────────────────
            // GROUP : loads
            // ──────────────────────────────────────────
            $table->boolean('can_view_loads')->default(false);
            $table->boolean('can_cancel_loads')->default(false);
            $table->boolean('can_delete_loads')->default(false);
            $table->boolean('can_print_loads')->default(false);

            // ──────────────────────────────────────────
            // GROUP : transactions
            // ──────────────────────────────────────────
            $table->boolean('can_view_transactions')->default(false);
            $table->boolean('can_refund_transactions')->default(false);
            $table->boolean('can_update_transaction_status')->default(false);
            $table->boolean('can_export_transactions')->default(false);
            $table->boolean('can_print_transactions')->default(false);

            // ──────────────────────────────────────────
            // GROUP : escrow
            // ──────────────────────────────────────────
            $table->boolean('can_view_escrow')->default(false);
            $table->boolean('can_release_escrow')->default(false);
            $table->boolean('can_refund_escrow')->default(false);
            $table->boolean('can_activate_escrow')->default(false);
            $table->boolean('can_open_escrow_dispute')->default(false);
            $table->boolean('can_resolve_escrow_dispute')->default(false);
            $table->boolean('can_admin_approve_escrow')->default(false);
            $table->boolean('can_cancel_escrow')->default(false);
            $table->boolean('can_export_escrow')->default(false);
            $table->boolean('can_print_escrow')->default(false);

            // ──────────────────────────────────────────
            // GROUP : commissions
            // ──────────────────────────────────────────
            $table->boolean('can_view_commissions')->default(false);
            $table->boolean('can_approve_commissions')->default(false);
            $table->boolean('can_mark_commissions_paid')->default(false);
            $table->boolean('can_cancel_commissions')->default(false);
            $table->boolean('can_bulk_approve_commissions')->default(false);
            $table->boolean('can_bulk_pay_commissions')->default(false);
            $table->boolean('can_export_commissions')->default(false);
            $table->boolean('can_manage_commission_settings')->default(false);
            $table->boolean('can_print_commissions')->default(false);

            // ──────────────────────────────────────────
            // GROUP : messages
            // ──────────────────────────────────────────
            $table->boolean('can_view_messages')->default(false);
            $table->boolean('can_send_broadcast_messages')->default(false);
            $table->boolean('can_create_vendor_groups')->default(false);

            // ──────────────────────────────────────────
            // GROUP : reports
            // ──────────────────────────────────────────
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_view_revenue_reports')->default(false);
            $table->boolean('can_view_user_reports')->default(false);
            $table->boolean('can_view_order_reports')->default(false);
            $table->boolean('can_view_vendor_reports')->default(false);
            $table->boolean('can_export_reports')->default(false);
            $table->boolean('can_print_reports')->default(false);

            // ──────────────────────────────────────────
            // GROUP : analytics
            // ──────────────────────────────────────────
            $table->boolean('can_view_analytics')->default(false);
            $table->boolean('can_view_regional_analytics')->default(false);
            $table->boolean('can_view_product_analytics')->default(false);
            $table->boolean('can_view_performance_analytics')->default(false);
            $table->boolean('can_print_analytics')->default(false);

            // ──────────────────────────────────────────
            // GROUP : settings
            // ──────────────────────────────────────────
            $table->boolean('can_view_settings')->default(false);
            $table->boolean('can_update_settings')->default(false);
            $table->boolean('can_view_general_settings')->default(false);
            $table->boolean('can_view_email_settings')->default(false);
            $table->boolean('can_view_payment_settings')->default(false);

            // ──────────────────────────────────────────
            // GROUP : security
            // ──────────────────────────────────────────
            $table->boolean('can_view_security')->default(false);
            $table->boolean('can_manage_two_factor')->default(false);
            $table->boolean('can_view_sessions')->default(false);
            $table->boolean('can_revoke_sessions')->default(false);
            $table->boolean('can_change_password')->default(false);

            // ──────────────────────────────────────────
            // GROUP : audit_logs
            // ──────────────────────────────────────────
            $table->boolean('can_view_audit_logs')->default(false);
            $table->boolean('can_export_audit_logs')->default(false);
            $table->boolean('can_print_audit_logs')->default(false);

            // ──────────────────────────────────────────
            // GROUP : addons
            // ──────────────────────────────────────────
            $table->boolean('can_view_addons')->default(false);
            $table->boolean('can_create_addons')->default(false);
            $table->boolean('can_edit_addons')->default(false);
            $table->boolean('can_delete_addons')->default(false);
            $table->boolean('can_print_addons')->default(false);

            // ──────────────────────────────────────────
            // GROUP : membership_plans
            // ──────────────────────────────────────────
            $table->boolean('can_view_membership_plans')->default(false);
            $table->boolean('can_create_membership_plans')->default(false);
            $table->boolean('can_edit_membership_plans')->default(false);
            $table->boolean('can_delete_membership_plans')->default(false);
            $table->boolean('can_toggle_membership_plan_status')->default(false);

            // ──────────────────────────────────────────
            // GROUP : membership_features
            // ──────────────────────────────────────────
            $table->boolean('can_view_plan_features')->default(false);
            $table->boolean('can_create_plan_features')->default(false);
            $table->boolean('can_edit_plan_features')->default(false);
            $table->boolean('can_delete_plan_features')->default(false);

            // ──────────────────────────────────────────
            // GROUP : subscriptions
            // ──────────────────────────────────────────
            $table->boolean('can_view_subscriptions')->default(false);
            $table->boolean('can_cancel_subscriptions')->default(false);
            $table->boolean('can_renew_subscriptions')->default(false);
            $table->boolean('can_change_subscription_plan')->default(false);

            // ──────────────────────────────────────────
            // GROUP : membership_settings
            // ──────────────────────────────────────────
            $table->boolean('can_view_membership_settings')->default(false);
            $table->boolean('can_update_membership_settings')->default(false);

            // ──────────────────────────────────────────
            // GROUP : configurations
            // ──────────────────────────────────────────
            $table->boolean('can_view_configurations')->default(false);
            $table->boolean('can_create_configurations')->default(false);
            $table->boolean('can_edit_configurations')->default(false);
            $table->boolean('can_delete_configurations')->default(false);
            $table->boolean('can_toggle_configuration_status')->default(false);
            $table->boolean('can_print_configurations')->default(false);

            // ──────────────────────────────────────────
            // GROUP : users
            // ──────────────────────────────────────────
            $table->boolean('can_view_users')->default(false);
            $table->boolean('can_suspend_users')->default(false);
            $table->boolean('can_activate_users')->default(false);

            $table->timestamps();

            // One permission record per user
            $table->unique('user_id');

            // Index for group-based queries
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manageable_permissions');
    }
};
