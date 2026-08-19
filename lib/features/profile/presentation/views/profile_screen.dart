import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_dialog.dart';
import '../../../auth/presentation/controllers/auth_controller.dart';
import '../controllers/profile_controller.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(patientProfileProvider);
    final dobFormatted = DateFormat('MMMM d, yyyy').format(profile.dateOfBirth);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        title: Text(
          'Patient Profile',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.settings_outlined, color: AppColors.onSurface),
            onPressed: () => context.push(AppRoutes.settings),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          children: [
            // 1. Profile Header Card
            AppCard(
              child: Column(
                children: [
                  AppAvatar(
                    name: profile.fullName,
                    imageUrl: profile.avatarUrl,
                    size: 80,
                  ),
                  AppSpacing.gapVMd,
                  Text(
                    profile.fullName,
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  AppSpacing.gapVXs,
                  Text(
                    profile.email,
                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                  Text(
                    profile.phoneNumber,
                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                  ),
                  AppSpacing.gapVMd,

                  // Health Quick Stats Badges
                  Container(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    decoration: BoxDecoration(
                      color: AppColors.surfaceContainerLow,
                      borderRadius: AppRadius.radiusMd,
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceAround,
                      children: [
                        _ProfileStat(label: 'Blood Type', value: profile.bloodType),
                        Container(width: 1, height: 24, color: AppColors.outlineVariant),
                        _ProfileStat(label: 'Weight', value: '${profile.weightKg} kg'),
                        Container(width: 1, height: 24, color: AppColors.outlineVariant),
                        _ProfileStat(label: 'Height', value: '${profile.heightCm} cm'),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Personal & Clinical Information
            _ProfileSectionHeader(title: 'Personal & Medical Information'),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                children: [
                  _ProfileDetailItem(
                    icon: Icons.cake_outlined,
                    label: 'Date of Birth',
                    value: dobFormatted,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 16),
                  _ProfileDetailItem(
                    icon: Icons.person_outline_rounded,
                    label: 'Gender',
                    value: profile.gender,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 16),
                  _ProfileDetailItem(
                    icon: Icons.home_outlined,
                    label: 'Home Address',
                    value: profile.address,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 16),
                  _ProfileDetailItem(
                    icon: Icons.emergency_outlined,
                    label: 'Emergency Contact',
                    value: '${profile.emergencyContactName} • ${profile.emergencyContactPhone}',
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Management Shortcuts
            _ProfileSectionHeader(title: 'Portal Management'),
            AppSpacing.gapVSm,
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  _ProfileMenuTile(
                    icon: Icons.edit_outlined,
                    title: 'Edit Personal Profile',
                    onTap: () => context.push(AppRoutes.editProfile),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  _ProfileMenuTile(
                    icon: Icons.description_outlined,
                    title: 'Medical Records',
                    onTap: () => context.push(AppRoutes.medicalRecords),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  _ProfileMenuTile(
                    icon: Icons.medication_outlined,
                    title: 'Prescriptions',
                    onTap: () => context.push(AppRoutes.prescriptions),
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 1),
                  _ProfileMenuTile(
                    icon: Icons.settings_outlined,
                    title: 'App Settings & Preferences',
                    onTap: () => context.push(AppRoutes.settings),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Sign Out Button
            AppCard(
              padding: EdgeInsets.zero,
              child: ListTile(
                leading: const Icon(Icons.logout_rounded, color: AppColors.error),
                title: Text(
                  'Sign Out',
                  style: AppTypography.titleMedium.copyWith(
                    color: AppColors.error,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                onTap: () async {
                  final confirmed = await AppDialog.showConfirmDialog(
                    context: context,
                    title: 'Sign Out',
                    message: 'Are you sure you want to sign out of your patient portal?',
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
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
    );
  }
}

class _ProfileSectionHeader extends StatelessWidget {
  final String title;

  const _ProfileSectionHeader({required this.title});

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: Alignment.centerLeft,
      child: Text(
        title,
        style: AppTypography.headlineSm.copyWith(
          color: AppColors.onSurface,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}

class _ProfileStat extends StatelessWidget {
  final String label;
  final String value;

  const _ProfileStat({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(
          value,
          style: AppTypography.titleMedium.copyWith(
            color: AppColors.primary,
            fontWeight: FontWeight.w800,
          ),
        ),
        AppSpacing.gapVXs,
        Text(
          label,
          style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
        ),
      ],
    );
  }
}

class _ProfileDetailItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _ProfileDetailItem({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 20, color: AppColors.primary),
        AppSpacing.gapHMd,
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
              AppSpacing.gapVXs,
              Text(
                value,
                style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface, fontWeight: FontWeight.w600),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _ProfileMenuTile extends StatelessWidget {
  final IconData icon;
  final String title;
  final VoidCallback onTap;

  const _ProfileMenuTile({
    required this.icon,
    required this.title,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: Icon(icon, color: AppColors.primary),
      title: Text(
        title,
        style: AppTypography.bodyMd.copyWith(
          color: AppColors.onSurface,
          fontWeight: FontWeight.w600,
        ),
      ),
      trailing: const Icon(Icons.chevron_right_rounded, color: AppColors.outline),
      onTap: onTap,
    );
  }
}
