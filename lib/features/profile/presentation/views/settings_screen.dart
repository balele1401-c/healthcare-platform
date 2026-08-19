import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
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
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Settings',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. ACCOUNT
            _SectionTitle(title: 'ACCOUNT'),
            AppSpacing.gapVSm,
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.person_outline_rounded, color: AppColors.primary),
                    title: Text('Edit Personal Profile', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                    onTap: () => context.push(AppRoutes.editProfile),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  ListTile(
                    leading: const Icon(Icons.lock_reset_rounded, color: AppColors.primary),
                    title: Text('Change Password', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
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
            _SectionTitle(title: 'PREFERENCES'),
            AppSpacing.gapVSm,
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  SwitchListTile(
                    secondary: const Icon(Icons.notifications_outlined, color: AppColors.primary),
                    title: Text('Push Notifications', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    subtitle: Text('Reminders, message alerts & refills', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                    value: profile.pushNotificationsEnabled,
                    activeThumbColor: AppColors.primary,
                    onChanged: (val) => notifier.togglePushNotifications(val),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  SwitchListTile(
                    secondary: const Icon(Icons.fingerprint_rounded, color: AppColors.primary),
                    title: Text('Biometric Login', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    subtitle: Text('Unlock app using Fingerprint / Face ID', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                    value: profile.biometricAuthEnabled,
                    activeThumbColor: AppColors.primary,
                    onChanged: (val) => notifier.toggleBiometrics(val),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  ListTile(
                    leading: const Icon(Icons.language_rounded, color: AppColors.primary),
                    title: Text('Language', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(profile.language, style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant)),
                        const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                      ],
                    ),
                    onTap: () {
                      AppSnackbar.showInfo(context, 'App language is currently locked to English (US).');
                    },
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. PRIVACY & COMPLIANCE
            _SectionTitle(title: 'PRIVACY & SECURITY'),
            AppSpacing.gapVSm,
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.security_outlined, color: AppColors.primary),
                    title: Text('HIPAA & Patient Data Protection', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                    onTap: () {
                      AppSnackbar.showInfo(context, 'All clinical data is encrypted in compliance with HIPAA standard.');
                    },
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  ListTile(
                    leading: const Icon(Icons.privacy_tip_outlined, color: AppColors.primary),
                    title: Text('Privacy Policy', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                    onTap: () {
                      AppSnackbar.showInfo(context, 'Opening HealthCare Privacy Policy...');
                    },
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. SUPPORT & ABOUT
            _SectionTitle(title: 'SUPPORT'),
            AppSpacing.gapVSm,
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  ListTile(
                    leading: const Icon(Icons.help_outline_rounded, color: AppColors.primary),
                    title: Text('Help Center & FAQs', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
                    onTap: () => AppSnackbar.showInfo(context, 'Help center documentation opened.'),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  ListTile(
                    leading: const Icon(Icons.info_outline_rounded, color: AppColors.primary),
                    title: Text('About HealthCare Platform', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    trailing: Text('v1.0.0 (Phase 2)', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 5. Sign Out Action
            AppButton(
              text: 'Sign Out of Account',
              variant: ButtonVariant.error,
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
            AppSpacing.gapV2Xl,
          ],
        ),
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
        color: AppColors.outline,
        fontWeight: FontWeight.w700,
        letterSpacing: 0.8,
      ),
    );
  }
}
