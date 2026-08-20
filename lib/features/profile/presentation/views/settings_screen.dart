import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_dialog.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../auth/presentation/controllers/auth_controller.dart';
import '../controllers/profile_controller.dart';

class SettingsScreen extends ConsumerWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(patientProfileProvider);
    final notifier = ref.read(patientProfileProvider.notifier);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Settings & Privacy',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth >= 900;

          return SingleChildScrollView(
            padding: EdgeInsets.symmetric(
              horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
              vertical: AppSpacing.lg,
            ),
            child: Center(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 860),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // 1. ACCOUNT
                    const _SectionTitle(title: 'ACCOUNT CREDENTIALS'),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: EdgeInsets.zero,
                      child: Column(
                        children: [
                          ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.person_outline_rounded, color: AppColors.primary, size: 20),
                            ),
                            title: Text('Edit Personal Profile', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            subtitle: Text('Name, email, phone number, address', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                            trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                            onTap: () => context.push(AppRoutes.editProfile),
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 1),
                          ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.lock_reset_rounded, color: AppColors.primary, size: 20),
                            ),
                            title: Text('Security & Password', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            subtitle: Text('Change password and manage 2FA', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                            trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                            onTap: () {
                              AppSnackbar.showInfo(context, 'Password change verification email dispatched.');
                            },
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 2. PREFERENCES
                    const _SectionTitle(title: 'SYSTEM PREFERENCES'),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: EdgeInsets.zero,
                      child: Column(
                        children: [
                          SwitchListTile(
                            secondary: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.notifications_outlined, color: AppColors.primary, size: 20),
                            ),
                            title: Text('Push Notifications', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            subtitle: Text('Consultation alerts, medication refills & chat messages', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                            value: profile.pushNotificationsEnabled,
                            activeThumbColor: AppColors.primary,
                            onChanged: (val) => notifier.togglePushNotifications(val),
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 1),
                          SwitchListTile(
                            secondary: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.fingerprint_rounded, color: AppColors.primary, size: 20),
                            ),
                            title: Text('Biometric Authentication', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            subtitle: Text('Unlock clinical dashboard via Fingerprint / Face ID', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                            value: profile.biometricAuthEnabled,
                            activeThumbColor: AppColors.primary,
                            onChanged: (val) => notifier.toggleBiometrics(val),
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 1),
                          ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.language_rounded, color: AppColors.primary, size: 20),
                            ),
                            title: Text('Application Language', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            trailing: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Text(profile.language, style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant, fontWeight: FontWeight.w600)),
                                const SizedBox(width: 4),
                                const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                              ],
                            ),
                            onTap: () {
                              AppSnackbar.showInfo(context, 'App language is currently set to English (US).');
                            },
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 3. PRIVACY & COMPLIANCE
                    const _SectionTitle(title: 'PRIVACY & HEALTHCARE COMPLIANCE'),
                    AppSpacing.gapVSm,
                    AppCard(
                      padding: EdgeInsets.zero,
                      child: Column(
                        children: [
                          ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.security_outlined, color: AppColors.secondary, size: 20),
                            ),
                            title: Text('HIPAA & Data Protection Standards', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            subtitle: Text('256-bit AES encryption and secure EHR transmission', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                            trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                            onTap: () {
                              AppSnackbar.showInfo(context, 'All clinical data is encrypted in compliance with HIPAA standards.');
                            },
                          ),
                          const Divider(color: AppColors.outlineVariant, height: 1),
                          ListTile(
                            leading: Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                borderRadius: AppRadius.radiusMd,
                              ),
                              child: const Icon(Icons.privacy_tip_outlined, color: AppColors.secondary, size: 20),
                            ),
                            title: Text('Privacy Policy & Clinical Consent', style: AppTypography.titleMedium.copyWith(fontSize: 15, fontWeight: FontWeight.w600)),
                            trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                            onTap: () {
                              AppSnackbar.showInfo(context, 'Opening HealthCare Terms & Privacy Policy...');
                            },
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 4. DANGER ZONE / SIGN OUT
                    const _SectionTitle(title: 'SESSION MANAGEMENT'),
                    AppSpacing.gapVSm,
                    AppButton(
                      text: 'Sign Out of Patient Account',
                      variant: ButtonVariant.error,
                      prefixIcon: Icons.logout_rounded,
                      onPressed: () async {
                        final confirmed = await AppDialog.showConfirmDialog(
                          context: context,
                          title: 'Sign Out',
                          message: 'Are you sure you want to sign out of your patient portal session?',
                          confirmText: 'Sign Out',
                          cancelText: 'Cancel',
                          isDestructive: true,
                        );

                        if (confirmed == true && context.mounted) {
                          await ref.read(authControllerProvider.notifier).logout();
                          if (context.mounted) {
                            context.go(AppRoutes.login);
                          }
                        }
                      },
                    ),
                    AppSpacing.gapVXxl,
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String title;

  const _SectionTitle({required this.title});

  @override
  Widget build(BuildContext context) {
    return Text(
      title,
      style: AppTypography.labelMd.copyWith(
        color: AppColors.onSurfaceVariant,
        fontWeight: FontWeight.w700,
        letterSpacing: 0.8,
      ),
    );
  }
}
