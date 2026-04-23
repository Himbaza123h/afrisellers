<?php

namespace App\Models;

use App\Traits\LogsActivity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ManageablePermission extends Model
{
    use LogsActivity;
    protected $table = 'manageable_permissions';

    protected $fillable = [
        'user_id',
        'group',

        // ── dashboard ──────────────────────────────
        'can_view_dashboard',

        // ── vendors ────────────────────────────────
        'can_view_vendors',
        'can_create_vendors',
        'can_edit_vendors',
        'can_verify_vendors',

        // ── regional_admins ────────────────────────
        'can_view_regional_admins',
        'can_create_regional_admins',
        'can_edit_regional_admins',
        'can_delete_regional_admins',
        'can_activate_regional_admins',
        'can_deactivate_regional_admins',
        'can_switch_to_regional_admin',
        'can_assign_regional_admin_user',
        'can_print_regional_admins',

        // ── countries ──────────────────────────────
        'can_view_countries',
        'can_create_countries',
        'can_edit_countries',
        'can_delete_countries',
        'can_activate_countries',
        'can_deactivate_countries',
        'can_switch_to_country',
        'can_assign_country_admin',
        'can_toggle_country_status',
        'can_print_countries',

        // ── business_profiles ──────────────────────
        'can_view_business_profiles',
        'can_verify_business_profiles',
        'can_reject_business_profiles',
        'can_suspend_business_profiles',
        'can_activate_business_profiles',
        'can_switch_to_vendor',
        'can_print_business_profiles',

        // ── buyers ─────────────────────────────────
        'can_view_buyers',
        'can_create_buyers',
        'can_edit_buyers',
        'can_delete_buyers',
        'can_suspend_buyers',
        'can_activate_buyers',
        'can_switch_to_buyer',
        'can_update_buyer_status',
        'can_print_buyers',

        // ── agents ─────────────────────────────────
        'can_view_agents',
        'can_create_agents',
        'can_edit_agents',
        'can_delete_agents',
        'can_verify_agents',
        'can_suspend_agents',
        'can_switch_to_agent',
        'can_print_agents',

        // ── transporters ───────────────────────────
        'can_view_transporters',
        'can_create_transporters',
        'can_edit_transporters',
        'can_delete_transporters',
        'can_verify_transporters',
        'can_suspend_transporters',
        'can_print_transporters',

        // ── product_categories ─────────────────────
        'can_view_product_categories',
        'can_create_product_categories',
        'can_edit_product_categories',
        'can_delete_product_categories',
        'can_activate_product_categories',
        'can_toggle_product_category_status',
        'can_print_product_categories',

        // ── products ───────────────────────────────
        'can_view_products',
        'can_create_products',
        'can_edit_products',
        'can_delete_products',
        'can_approve_products',
        'can_reject_products',
        'can_feature_products',
        'can_toggle_product_verification',
        'can_print_products',

        // ── vendor_products ────────────────────────
        'can_view_vendor_products',
        'can_create_vendor_products',
        'can_edit_vendor_products',
        'can_delete_vendor_products',
        'can_approve_vendor_products',
        'can_reject_vendor_products',
        'can_bulk_delete_vendor_products',
        'can_bulk_status_vendor_products',
        'can_bulk_upload_vendor_products',
        'can_manage_vendor_product_images',
        'can_download_vendor_product_template',
        'can_print_vendor_products',

        // ── orders ─────────────────────────────────
        'can_view_orders',
        'can_create_orders',
        'can_edit_orders',
        'can_cancel_orders',
        'can_accept_orders',
        'can_process_orders',
        'can_ship_orders',
        'can_complete_orders',
        'can_refund_orders',
        'can_view_order_invoice',
        'can_download_order_invoice',
        'can_print_orders',

        // ── rfqs ───────────────────────────────────
        'can_view_rfqs',
        'can_create_rfqs',
        'can_edit_rfqs',
        'can_delete_rfqs',
        'can_approve_rfqs',
        'can_reject_rfqs',
        'can_view_rfq_vendors',
        'can_view_rfq_messages',
        'can_send_rfq_messages',
        'can_print_rfqs',

        // ── showrooms ──────────────────────────────
        'can_view_showrooms',
        'can_verify_showrooms',
        'can_unverify_showrooms',
        'can_feature_showrooms',
        'can_activate_showrooms',
        'can_suspend_showrooms',
        'can_delete_showrooms',
        'can_print_showrooms',

        // ── tradeshows ─────────────────────────────
        'can_view_tradeshows',
        'can_approve_tradeshows',
        'can_verify_tradeshows',
        'can_unverify_tradeshows',
        'can_feature_tradeshows',
        'can_suspend_tradeshows',
        'can_delete_tradeshows',
        'can_print_tradeshows',

        // ── loads ──────────────────────────────────
        'can_view_loads',
        'can_cancel_loads',
        'can_delete_loads',
        'can_print_loads',

        // ── transactions ───────────────────────────
        'can_view_transactions',
        'can_refund_transactions',
        'can_update_transaction_status',
        'can_export_transactions',
        'can_print_transactions',

        // ── escrow ─────────────────────────────────
        'can_view_escrow',
        'can_release_escrow',
        'can_refund_escrow',
        'can_activate_escrow',
        'can_open_escrow_dispute',
        'can_resolve_escrow_dispute',
        'can_admin_approve_escrow',
        'can_cancel_escrow',
        'can_export_escrow',
        'can_print_escrow',

        // ── commissions ────────────────────────────
        'can_view_commissions',
        'can_approve_commissions',
        'can_mark_commissions_paid',
        'can_cancel_commissions',
        'can_bulk_approve_commissions',
        'can_bulk_pay_commissions',
        'can_export_commissions',
        'can_manage_commission_settings',
        'can_print_commissions',

        // ── messages ───────────────────────────────
        'can_view_messages',
        'can_send_broadcast_messages',
        'can_create_vendor_groups',

        // ── reports ────────────────────────────────
        'can_view_reports',
        'can_view_revenue_reports',
        'can_view_user_reports',
        'can_view_order_reports',
        'can_view_vendor_reports',
        'can_export_reports',
        'can_print_reports',

        // ── analytics ──────────────────────────────
        'can_view_analytics',
        'can_view_regional_analytics',
        'can_view_product_analytics',
        'can_view_performance_analytics',
        'can_print_analytics',

        // ── settings ───────────────────────────────
        'can_view_settings',
        'can_update_settings',
        'can_view_general_settings',
        'can_view_email_settings',
        'can_view_payment_settings',

        // ── security ───────────────────────────────
        'can_view_security',
        'can_manage_two_factor',
        'can_view_sessions',
        'can_revoke_sessions',
        'can_change_password',

        // ── audit_logs ─────────────────────────────
        'can_view_audit_logs',
        'can_export_audit_logs',
        'can_print_audit_logs',

        // ── addons ─────────────────────────────────
        'can_view_addons',
        'can_create_addons',
        'can_edit_addons',
        'can_delete_addons',
        'can_print_addons',

        // ── membership_plans ───────────────────────
        'can_view_membership_plans',
        'can_create_membership_plans',
        'can_edit_membership_plans',
        'can_delete_membership_plans',
        'can_toggle_membership_plan_status',

        // ── membership_features ────────────────────
        'can_view_plan_features',
        'can_create_plan_features',
        'can_edit_plan_features',
        'can_delete_plan_features',

        // ── subscriptions ──────────────────────────
        'can_view_subscriptions',
        'can_cancel_subscriptions',
        'can_renew_subscriptions',
        'can_change_subscription_plan',

        // ── membership_settings ────────────────────
        'can_view_membership_settings',
        'can_update_membership_settings',

        // ── configurations ─────────────────────────
        'can_view_configurations',
        'can_create_configurations',
        'can_edit_configurations',
        'can_delete_configurations',
        'can_toggle_configuration_status',
        'can_print_configurations',

        // ── users ──────────────────────────────────
        'can_view_users',
        'can_suspend_users',
        'can_activate_users',
    ];

    protected $casts = [
        // dashboard
        'can_view_dashboard'                    => 'boolean',
        // vendors
        'can_view_vendors'                      => 'boolean',
        'can_create_vendors'                    => 'boolean',
        'can_edit_vendors'                      => 'boolean',
        'can_verify_vendors'                    => 'boolean',
        // regional_admins
        'can_view_regional_admins'              => 'boolean',
        'can_create_regional_admins'            => 'boolean',
        'can_edit_regional_admins'              => 'boolean',
        'can_delete_regional_admins'            => 'boolean',
        'can_activate_regional_admins'          => 'boolean',
        'can_deactivate_regional_admins'        => 'boolean',
        'can_switch_to_regional_admin'          => 'boolean',
        'can_assign_regional_admin_user'        => 'boolean',
        'can_print_regional_admins'             => 'boolean',
        // countries
        'can_view_countries'                    => 'boolean',
        'can_create_countries'                  => 'boolean',
        'can_edit_countries'                    => 'boolean',
        'can_delete_countries'                  => 'boolean',
        'can_activate_countries'                => 'boolean',
        'can_deactivate_countries'              => 'boolean',
        'can_switch_to_country'                 => 'boolean',
        'can_assign_country_admin'              => 'boolean',
        'can_toggle_country_status'             => 'boolean',
        'can_print_countries'                   => 'boolean',
        // business_profiles
        'can_view_business_profiles'            => 'boolean',
        'can_verify_business_profiles'          => 'boolean',
        'can_reject_business_profiles'          => 'boolean',
        'can_suspend_business_profiles'         => 'boolean',
        'can_activate_business_profiles'        => 'boolean',
        'can_switch_to_vendor'                  => 'boolean',
        'can_print_business_profiles'           => 'boolean',
        // buyers
        'can_view_buyers'                       => 'boolean',
        'can_create_buyers'                     => 'boolean',
        'can_edit_buyers'                       => 'boolean',
        'can_delete_buyers'                     => 'boolean',
        'can_suspend_buyers'                    => 'boolean',
        'can_activate_buyers'                   => 'boolean',
        'can_switch_to_buyer'                   => 'boolean',
        'can_update_buyer_status'               => 'boolean',
        'can_print_buyers'                      => 'boolean',
        // agents
        'can_view_agents'                       => 'boolean',
        'can_create_agents'                     => 'boolean',
        'can_edit_agents'                       => 'boolean',
        'can_delete_agents'                     => 'boolean',
        'can_verify_agents'                     => 'boolean',
        'can_suspend_agents'                    => 'boolean',
        'can_switch_to_agent'                   => 'boolean',
        'can_print_agents'                      => 'boolean',
        // transporters
        'can_view_transporters'                 => 'boolean',
        'can_create_transporters'               => 'boolean',
        'can_edit_transporters'                 => 'boolean',
        'can_delete_transporters'               => 'boolean',
        'can_verify_transporters'               => 'boolean',
        'can_suspend_transporters'              => 'boolean',
        'can_print_transporters'                => 'boolean',
        // product_categories
        'can_view_product_categories'           => 'boolean',
        'can_create_product_categories'         => 'boolean',
        'can_edit_product_categories'           => 'boolean',
        'can_delete_product_categories'         => 'boolean',
        'can_activate_product_categories'       => 'boolean',
        'can_toggle_product_category_status'    => 'boolean',
        'can_print_product_categories'          => 'boolean',
        // products
        'can_view_products'                     => 'boolean',
        'can_create_products'                   => 'boolean',
        'can_edit_products'                     => 'boolean',
        'can_delete_products'                   => 'boolean',
        'can_approve_products'                  => 'boolean',
        'can_reject_products'                   => 'boolean',
        'can_feature_products'                  => 'boolean',
        'can_toggle_product_verification'       => 'boolean',
        'can_print_products'                    => 'boolean',
        // vendor_products
        'can_view_vendor_products'              => 'boolean',
        'can_create_vendor_products'            => 'boolean',
        'can_edit_vendor_products'              => 'boolean',
        'can_delete_vendor_products'            => 'boolean',
        'can_approve_vendor_products'           => 'boolean',
        'can_reject_vendor_products'            => 'boolean',
        'can_bulk_delete_vendor_products'       => 'boolean',
        'can_bulk_status_vendor_products'       => 'boolean',
        'can_bulk_upload_vendor_products'       => 'boolean',
        'can_manage_vendor_product_images'      => 'boolean',
        'can_download_vendor_product_template'  => 'boolean',
        'can_print_vendor_products'             => 'boolean',
        // orders
        'can_view_orders'                       => 'boolean',
        'can_create_orders'                     => 'boolean',
        'can_edit_orders'                       => 'boolean',
        'can_cancel_orders'                     => 'boolean',
        'can_accept_orders'                     => 'boolean',
        'can_process_orders'                    => 'boolean',
        'can_ship_orders'                       => 'boolean',
        'can_complete_orders'                   => 'boolean',
        'can_refund_orders'                     => 'boolean',
        'can_view_order_invoice'                => 'boolean',
        'can_download_order_invoice'            => 'boolean',
        'can_print_orders'                      => 'boolean',
        // rfqs
        'can_view_rfqs'                         => 'boolean',
        'can_create_rfqs'                       => 'boolean',
        'can_edit_rfqs'                         => 'boolean',
        'can_delete_rfqs'                       => 'boolean',
        'can_approve_rfqs'                      => 'boolean',
        'can_reject_rfqs'                       => 'boolean',
        'can_view_rfq_vendors'                  => 'boolean',
        'can_view_rfq_messages'                 => 'boolean',
        'can_send_rfq_messages'                 => 'boolean',
        'can_print_rfqs'                        => 'boolean',
        // showrooms
        'can_view_showrooms'                    => 'boolean',
        'can_verify_showrooms'                  => 'boolean',
        'can_unverify_showrooms'                => 'boolean',
        'can_feature_showrooms'                 => 'boolean',
        'can_activate_showrooms'                => 'boolean',
        'can_suspend_showrooms'                 => 'boolean',
        'can_delete_showrooms'                  => 'boolean',
        'can_print_showrooms'                   => 'boolean',
        // tradeshows
        'can_view_tradeshows'                   => 'boolean',
        'can_approve_tradeshows'                => 'boolean',
        'can_verify_tradeshows'                 => 'boolean',
        'can_unverify_tradeshows'               => 'boolean',
        'can_feature_tradeshows'                => 'boolean',
        'can_suspend_tradeshows'                => 'boolean',
        'can_delete_tradeshows'                 => 'boolean',
        'can_print_tradeshows'                  => 'boolean',
        // loads
        'can_view_loads'                        => 'boolean',
        'can_cancel_loads'                      => 'boolean',
        'can_delete_loads'                      => 'boolean',
        'can_print_loads'                       => 'boolean',
        // transactions
        'can_view_transactions'                 => 'boolean',
        'can_refund_transactions'               => 'boolean',
        'can_update_transaction_status'         => 'boolean',
        'can_export_transactions'               => 'boolean',
        'can_print_transactions'                => 'boolean',
        // escrow
        'can_view_escrow'                       => 'boolean',
        'can_release_escrow'                    => 'boolean',
        'can_refund_escrow'                     => 'boolean',
        'can_activate_escrow'                   => 'boolean',
        'can_open_escrow_dispute'               => 'boolean',
        'can_resolve_escrow_dispute'            => 'boolean',
        'can_admin_approve_escrow'              => 'boolean',
        'can_cancel_escrow'                     => 'boolean',
        'can_export_escrow'                     => 'boolean',
        'can_print_escrow'                      => 'boolean',
        // commissions
        'can_view_commissions'                  => 'boolean',
        'can_approve_commissions'               => 'boolean',
        'can_mark_commissions_paid'             => 'boolean',
        'can_cancel_commissions'                => 'boolean',
        'can_bulk_approve_commissions'          => 'boolean',
        'can_bulk_pay_commissions'              => 'boolean',
        'can_export_commissions'                => 'boolean',
        'can_manage_commission_settings'        => 'boolean',
        'can_print_commissions'                 => 'boolean',
        // messages
        'can_view_messages'                     => 'boolean',
        'can_send_broadcast_messages'           => 'boolean',
        'can_create_vendor_groups'              => 'boolean',
        // reports
        'can_view_reports'                      => 'boolean',
        'can_view_revenue_reports'              => 'boolean',
        'can_view_user_reports'                 => 'boolean',
        'can_view_order_reports'                => 'boolean',
        'can_view_vendor_reports'               => 'boolean',
        'can_export_reports'                    => 'boolean',
        'can_print_reports'                     => 'boolean',
        // analytics
        'can_view_analytics'                    => 'boolean',
        'can_view_regional_analytics'           => 'boolean',
        'can_view_product_analytics'            => 'boolean',
        'can_view_performance_analytics'        => 'boolean',
        'can_print_analytics'                   => 'boolean',
        // settings
        'can_view_settings'                     => 'boolean',
        'can_update_settings'                   => 'boolean',
        'can_view_general_settings'             => 'boolean',
        'can_view_email_settings'               => 'boolean',
        'can_view_payment_settings'             => 'boolean',
        // security
        'can_view_security'                     => 'boolean',
        'can_manage_two_factor'                 => 'boolean',
        'can_view_sessions'                     => 'boolean',
        'can_revoke_sessions'                   => 'boolean',
        'can_change_password'                   => 'boolean',
        // audit_logs
        'can_view_audit_logs'                   => 'boolean',
        'can_export_audit_logs'                 => 'boolean',
        'can_print_audit_logs'                  => 'boolean',
        // addons
        'can_view_addons'                       => 'boolean',
        'can_create_addons'                     => 'boolean',
        'can_edit_addons'                       => 'boolean',
        'can_delete_addons'                     => 'boolean',
        'can_print_addons'                      => 'boolean',
        // membership_plans
        'can_view_membership_plans'             => 'boolean',
        'can_create_membership_plans'           => 'boolean',
        'can_edit_membership_plans'             => 'boolean',
        'can_delete_membership_plans'           => 'boolean',
        'can_toggle_membership_plan_status'     => 'boolean',
        // membership_features
        'can_view_plan_features'                => 'boolean',
        'can_create_plan_features'              => 'boolean',
        'can_edit_plan_features'                => 'boolean',
        'can_delete_plan_features'              => 'boolean',
        // subscriptions
        'can_view_subscriptions'                => 'boolean',
        'can_cancel_subscriptions'              => 'boolean',
        'can_renew_subscriptions'               => 'boolean',
        'can_change_subscription_plan'          => 'boolean',
        // membership_settings
        'can_view_membership_settings'          => 'boolean',
        'can_update_membership_settings'        => 'boolean',
        // configurations
        'can_view_configurations'               => 'boolean',
        'can_create_configurations'             => 'boolean',
        'can_edit_configurations'               => 'boolean',
        'can_delete_configurations'             => 'boolean',
        'can_toggle_configuration_status'       => 'boolean',
        'can_print_configurations'              => 'boolean',
        // users
        'can_view_users'                        => 'boolean',
        'can_suspend_users'                     => 'boolean',
        'can_activate_users'                    => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────────
    // GROUP MAP  —  which columns belong to each group
    // Used to power grantGroup(), revokeGroup(), and permission UIs
    // ─────────────────────────────────────────────────────────────────
    public const GROUPS = [
        'dashboard'            => ['can_view_dashboard'],
        'vendors'              => ['can_view_vendors','can_create_vendors','can_edit_vendors','can_verify_vendors'],
        'regional_admins'      => ['can_view_regional_admins','can_create_regional_admins','can_edit_regional_admins','can_delete_regional_admins','can_activate_regional_admins','can_deactivate_regional_admins','can_switch_to_regional_admin','can_assign_regional_admin_user','can_print_regional_admins'],
        'countries'            => ['can_view_countries','can_create_countries','can_edit_countries','can_delete_countries','can_activate_countries','can_deactivate_countries','can_switch_to_country','can_assign_country_admin','can_toggle_country_status','can_print_countries'],
        'business_profiles'    => ['can_view_business_profiles','can_verify_business_profiles','can_reject_business_profiles','can_suspend_business_profiles','can_activate_business_profiles','can_switch_to_vendor','can_print_business_profiles'],
        'buyers'               => ['can_view_buyers','can_create_buyers','can_edit_buyers','can_delete_buyers','can_suspend_buyers','can_activate_buyers','can_switch_to_buyer','can_update_buyer_status','can_print_buyers'],
        'agents'               => ['can_view_agents','can_create_agents','can_edit_agents','can_delete_agents','can_verify_agents','can_suspend_agents','can_switch_to_agent','can_print_agents'],
        'transporters'         => ['can_view_transporters','can_create_transporters','can_edit_transporters','can_delete_transporters','can_verify_transporters','can_suspend_transporters','can_print_transporters'],
        'product_categories'   => ['can_view_product_categories','can_create_product_categories','can_edit_product_categories','can_delete_product_categories','can_activate_product_categories','can_toggle_product_category_status','can_print_product_categories'],
        'products'             => ['can_view_products','can_create_products','can_edit_products','can_delete_products','can_approve_products','can_reject_products','can_feature_products','can_toggle_product_verification','can_print_products'],
        'vendor_products'      => ['can_view_vendor_products','can_create_vendor_products','can_edit_vendor_products','can_delete_vendor_products','can_approve_vendor_products','can_reject_vendor_products','can_bulk_delete_vendor_products','can_bulk_status_vendor_products','can_bulk_upload_vendor_products','can_manage_vendor_product_images','can_download_vendor_product_template','can_print_vendor_products'],
        'orders'               => ['can_view_orders','can_create_orders','can_edit_orders','can_cancel_orders','can_accept_orders','can_process_orders','can_ship_orders','can_complete_orders','can_refund_orders','can_view_order_invoice','can_download_order_invoice','can_print_orders'],
        'rfqs'                 => ['can_view_rfqs','can_create_rfqs','can_edit_rfqs','can_delete_rfqs','can_approve_rfqs','can_reject_rfqs','can_view_rfq_vendors','can_view_rfq_messages','can_send_rfq_messages','can_print_rfqs'],
        'showrooms'            => ['can_view_showrooms','can_verify_showrooms','can_unverify_showrooms','can_feature_showrooms','can_activate_showrooms','can_suspend_showrooms','can_delete_showrooms','can_print_showrooms'],
        'tradeshows'           => ['can_view_tradeshows','can_approve_tradeshows','can_verify_tradeshows','can_unverify_tradeshows','can_feature_tradeshows','can_suspend_tradeshows','can_delete_tradeshows','can_print_tradeshows'],
        'loads'                => ['can_view_loads','can_cancel_loads','can_delete_loads','can_print_loads'],
        'transactions'         => ['can_view_transactions','can_refund_transactions','can_update_transaction_status','can_export_transactions','can_print_transactions'],
        'escrow'               => ['can_view_escrow','can_release_escrow','can_refund_escrow','can_activate_escrow','can_open_escrow_dispute','can_resolve_escrow_dispute','can_admin_approve_escrow','can_cancel_escrow','can_export_escrow','can_print_escrow'],
        'commissions'          => ['can_view_commissions','can_approve_commissions','can_mark_commissions_paid','can_cancel_commissions','can_bulk_approve_commissions','can_bulk_pay_commissions','can_export_commissions','can_manage_commission_settings','can_print_commissions'],
        'messages'             => ['can_view_messages','can_send_broadcast_messages','can_create_vendor_groups'],
        'reports'              => ['can_view_reports','can_view_revenue_reports','can_view_user_reports','can_view_order_reports','can_view_vendor_reports','can_export_reports','can_print_reports'],
        'analytics'            => ['can_view_analytics','can_view_regional_analytics','can_view_product_analytics','can_view_performance_analytics','can_print_analytics'],
        'settings'             => ['can_view_settings','can_update_settings','can_view_general_settings','can_view_email_settings','can_view_payment_settings'],
        'security'             => ['can_view_security','can_manage_two_factor','can_view_sessions','can_revoke_sessions','can_change_password'],
        'audit_logs'           => ['can_view_audit_logs','can_export_audit_logs','can_print_audit_logs'],
        'addons'               => ['can_view_addons','can_create_addons','can_edit_addons','can_delete_addons','can_print_addons'],
        'membership_plans'     => ['can_view_membership_plans','can_create_membership_plans','can_edit_membership_plans','can_delete_membership_plans','can_toggle_membership_plan_status'],
        'membership_features'  => ['can_view_plan_features','can_create_plan_features','can_edit_plan_features','can_delete_plan_features'],
        'subscriptions'        => ['can_view_subscriptions','can_cancel_subscriptions','can_renew_subscriptions','can_change_subscription_plan'],
        'membership_settings'  => ['can_view_membership_settings','can_update_membership_settings'],
        'configurations'       => ['can_view_configurations','can_create_configurations','can_edit_configurations','can_delete_configurations','can_toggle_configuration_status','can_print_configurations'],
        'users'                => ['can_view_users','can_suspend_users','can_activate_users'],
    ];

    // ─────────────────────────────────────────────────────────────────
    // RELATIONSHIP
    // ─────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Grant every can_* column for a user.
     */
    public function grantAll(): static
    {
        $this->fill($this->allPermissionsAs(true))->save();
        return $this;
    }

    /**
     * Revoke every can_* column for a user.
     */
    public function revokeAll(): static
    {
        $this->fill($this->allPermissionsAs(false))->save();
        return $this;
    }

    /**
     * Grant all permissions in a named group.
     *
     * $permission->grantGroup('orders');
     */
    public function grantGroup(string $group): static
    {
        $cols = self::GROUPS[$group] ?? [];
        $this->fill(array_fill_keys($cols, true))->save();
        return $this;
    }

    /**
     * Revoke all permissions in a named group.
     */
    public function revokeGroup(string $group): static
    {
        $cols = self::GROUPS[$group] ?? [];
        $this->fill(array_fill_keys($cols, false))->save();
        return $this;
    }

    /**
     * Check a single permission on this record.
     *
     * $permission->can('can_approve_orders')  // true|false
     */
    public function can(string $permission): bool
    {
        return (bool) ($this->$permission ?? false);
    }

    /**
     * Return all can_* columns grouped, perfect for building a UI.
     *
     * [
     *   'vendors'  => ['can_view_vendors' => false, 'can_create_vendors' => true, ...],
     *   'orders'   => [...],
     * ]
     */
    public function byGroup(): Collection
    {
        return collect(self::GROUPS)->map(
            fn($cols) => collect($cols)->mapWithKeys(fn($col) => [$col => (bool) ($this->$col ?? false)])
        );
    }

    /**
     * Return every can_* column name.
     */
    public static function allPermissionKeys(): array
    {
        return collect(self::GROUPS)->flatten()->values()->all();
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE
    // ─────────────────────────────────────────────────────────────────

    private function allPermissionsAs(bool $value): array
    {
        return array_fill_keys(static::allPermissionKeys(), $value);
    }
}
