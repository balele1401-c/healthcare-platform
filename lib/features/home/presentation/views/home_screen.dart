import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_loading.dart';
import '../../../appointment/presentation/controllers/appointment_controller.dart';
import '../../../doctor/presentation/controllers/doctor_controller.dart';
import '../../../doctor/presentation/widgets/doctor_card.dart';
import '../../../health_tracker/presentation/controllers/health_tracker_controller.dart';
import '../../../notifications/presentation/controllers/notification_controller.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  String _getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'Good morning';
    if (hour < 17) return 'Good afternoon';
    return 'Good evening';
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profile = ref.watch(patientProfileProvider);
    final unreadCountAsync = ref.watch(unreadNotificationCountProvider);
    final nextAppointmentAsync = ref.watch(nextAppointmentProvider);
    final recommendedDocsAsync = ref.watch(recommendedDoctorsProvider);
    final healthMetricsAsync = ref.watch(healthMetricsProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        scrolledUnderElevation: 1,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(AppSpacing.xs),
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: AppRadius.radiusSm,
              ),
              child: const Icon(
                Icons.health_and_safety_rounded,
                color: AppColors.onPrimary,
                size: 20,
              ),
            ),
            AppSpacing.gapHSm,
            Text(
              'HealthCare',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.primary,
                fontWeight: FontWeight.w800,
              ),
            ),
          ],
        ),
        actions: [
          // Notifications Action with Badge
          IconButton(
            icon: Stack(
              clipBehavior: Clip.none,
              children: [
                const Icon(Icons.notifications_outlined, color: AppColors.onSurface, size: 26),
                unreadCountAsync.when(
                  data: (count) => count > 0
                      ? Positioned(
                          top: -2,
                          right: -2,
                          child: Container(
                            padding: const EdgeInsets.all(4),
                            decoration: const BoxDecoration(
                              color: AppColors.error,
                              shape: BoxShape.circle,
                            ),
                            constraints: const BoxConstraints(minWidth: 16, minHeight: 16),
                            child: Center(
                              child: Text(
                                '$count',
                                style: const TextStyle(
                                  color: AppColors.onError,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),
                        )
                      : const SizedBox.shrink(),
                  loading: () => const SizedBox.shrink(),
                  error: (_, __) => const SizedBox.shrink(),
                ),
              ],
            ),
            onPressed: () => context.push(AppRoutes.notifications),
          ),
          Padding(
            padding: const EdgeInsets.only(right: AppSpacing.md, left: AppSpacing.xs),
            child: GestureDetector(
              onTap: () => context.push(AppRoutes.profile),
              child: AppAvatar(
                name: profile.fullName,
                imageUrl: profile.avatarUrl,
                size: 36,
              ),
            ),
          ),
        ],
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          ref.invalidate(nextAppointmentProvider);
          ref.invalidate(recommendedDoctorsProvider);
          ref.invalidate(healthMetricsProvider);
          ref.invalidate(unreadNotificationCountProvider);
        },
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.marginMobile,
            vertical: AppSpacing.md,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Patient Greeting
              Text(
                '${_getGreeting()}, ${profile.fullName.split(' ').first}',
                style: AppTypography.headlineLgMobile.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVXs,
              Text(
                'Here is your daily health overview and clinical appointments.',
                style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
              ),
              AppSpacing.gapVLg,

              // 2. Doctor Search Bar Trigger
              GestureDetector(
                onTap: () => context.push(AppRoutes.doctorSearch),
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: AppSpacing.md,
                    vertical: AppSpacing.md,
                  ),
                  decoration: BoxDecoration(
                    color: AppColors.surfaceContainerLowest,
                    borderRadius: AppRadius.radiusMd,
                    border: Border.all(color: AppColors.outlineVariant),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.search_rounded, color: AppColors.onSurfaceVariant, size: 22),
                      AppSpacing.gapHSm,
                      Expanded(
                        child: Text(
                          'Find doctors, clinics, or specialties...',
                          style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusSm,
                        ),
                        child: const Icon(Icons.tune_rounded, size: 18, color: AppColors.primary),
                      ),
                    ],
                  ),
                ),
              ),
              AppSpacing.gapV2Xl,

              // 3. Upcoming Appointment Floating Widget
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Upcoming Appointment',
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton(
                    onPressed: () => context.push(AppRoutes.myAppointments),
                    child: Text(
                      'See all',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ],
              ),
              AppSpacing.gapVSm,
              nextAppointmentAsync.when(
                data: (appointment) {
                  if (appointment == null) {
                    return AppCard(
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
                        child: AppEmptyState(
                          icon: Icons.calendar_today_outlined,
                          title: 'No Upcoming Appointments',
                          message: 'Schedule a visit with one of our specialized doctors today.',
                          actionText: 'Book a Doctor',
                          onAction: () => context.push(AppRoutes.doctorSearch),
                        ),
                      ),
                    );
                  }

                  return AppCard(
                    onTap: () => context.push(
                      AppRoutes.appointmentDetail,
                      extra: appointment,
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            AppAvatar(
                              name: appointment.doctorName,
                              imageUrl: appointment.doctorAvatarUrl,
                              size: 50,
                            ),
                            AppSpacing.gapHMd,
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    appointment.doctorName,
                                    style: AppTypography.titleMedium.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w700,
                                    ),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                  AppSpacing.gapVXs,
                                  Text(
                                    appointment.doctorSpecialty,
                                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.all(AppSpacing.sm),
                              decoration: BoxDecoration(
                                color: AppColors.surfaceContainerLow,
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.favorite_rounded,
                                size: 20,
                                color: AppColors.primary,
                              ),
                            ),
                          ],
                        ),
                        AppSpacing.gapVMd,

                        // Schedule & Consultation Mode Banner
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
                          decoration: BoxDecoration(
                            color: AppColors.surfaceContainer,
                            borderRadius: AppRadius.radiusMd,
                            border: Border.all(color: AppColors.surfaceVariant),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.calendar_today_rounded, size: 16, color: AppColors.primary),
                                  AppSpacing.gapHXs,
                                  Text(
                                    appointment.timeSlot,
                                    style: AppTypography.bodySm.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ],
                              ),
                              Container(width: 1, height: 16, color: AppColors.outlineVariant),
                              Row(
                                children: [
                                  Icon(appointment.consultationType.icon, size: 18, color: AppColors.primary),
                                  AppSpacing.gapHXs,
                                  Text(
                                    appointment.consultationType.label,
                                    style: AppTypography.bodySm.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                        AppSpacing.gapVMd,

                        // Action Button
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.primary,
                              foregroundColor: AppColors.onPrimary,
                              shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusBase),
                              padding: const EdgeInsets.symmetric(vertical: 12),
                            ),
                            icon: const Icon(Icons.video_call_rounded, size: 20),
                            label: Text(
                              'Join Consultation',
                              style: AppTypography.labelMd.copyWith(
                                color: AppColors.onPrimary,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            onPressed: () => context.push(
                              AppRoutes.appointmentDetail,
                              extra: appointment,
                            ),
                          ),
                        ),
                      ],
                    ),
                  );
                },
                loading: () => const AppCard(child: AppLoading(message: 'Loading schedule...')),
                error: (err, _) => const AppCard(child: AppError(message: 'Could not load appointment')),
              ),
              AppSpacing.gapV2Xl,

              // 4. Quick Actions Bento Grid (6 Actions)
              Text(
                'Quick Actions',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVMd,
              GridView.count(
                crossAxisCount: 2,
                crossAxisSpacing: AppSpacing.md,
                mainAxisSpacing: AppSpacing.md,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                childAspectRatio: 1.35,
                children: [
                  _QuickActionTile(
                    title: 'Find Doctor',
                    subtitle: 'Search specialists',
                    icon: Icons.search_rounded,
                    iconBgColor: AppColors.primaryFixed,
                    iconColor: AppColors.primary,
                    onTap: () => context.push(AppRoutes.doctorSearch),
                  ),
                  _QuickActionTile(
                    title: 'Appointments',
                    subtitle: 'Schedule & visits',
                    icon: Icons.calendar_month_rounded,
                    iconBgColor: AppColors.secondaryContainer,
                    iconColor: AppColors.secondary,
                    onTap: () => context.push(AppRoutes.myAppointments),
                  ),
                  _QuickActionTile(
                    title: 'Medical Records',
                    subtitle: 'Lab results & history',
                    icon: Icons.description_outlined,
                    iconBgColor: AppColors.surfaceContainerHighest,
                    iconColor: AppColors.onSurfaceVariant,
                    onTap: () => context.push(AppRoutes.medicalRecords),
                  ),
                  _QuickActionTile(
                    title: 'Prescriptions',
                    subtitle: 'Active medications',
                    icon: Icons.medication_outlined,
                    iconBgColor: const Color(0xFFE3FCEF),
                    iconColor: AppColors.success,
                    onTap: () => context.push(AppRoutes.prescriptions),
                  ),
                  _QuickActionTile(
                    title: 'Health Tracker',
                    subtitle: 'Vitals & BMI trends',
                    icon: Icons.favorite_border_rounded,
                    iconBgColor: const Color(0xFFFFEDEB),
                    iconColor: AppColors.error,
                    onTap: () => context.push(AppRoutes.healthTracker),
                  ),
                  _QuickActionTile(
                    title: 'AI Assistant',
                    subtitle: '24/7 health guidance',
                    icon: Icons.auto_awesome_rounded,
                    iconBgColor: const Color(0xFFF3E5F5),
                    iconColor: const Color(0xFF8E24AA),
                    onTap: () => context.push(AppRoutes.aiAssistant),
                  ),
                ],
              ),
              AppSpacing.gapV2Xl,

              // 5. Health Summary Section
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Health Summary',
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton(
                    onPressed: () => context.push(AppRoutes.healthTracker),
                    child: Text(
                      'Details',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ],
              ),
              AppSpacing.gapVSm,
              healthMetricsAsync.when(
                data: (metrics) {
                  final steps = metrics.firstWhere((m) => m.type.name == 'steps');
                  final heartRate = metrics.firstWhere((m) => m.type.name == 'heartRate');

                  return Row(
                    children: [
                      // Steps Card
                      Expanded(
                        child: AppCard(
                          padding: const EdgeInsets.all(AppSpacing.md),
                          onTap: () => context.push(AppRoutes.healthTracker),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.directions_walk_rounded, size: 20, color: AppColors.primary),
                                  AppSpacing.gapHXs,
                                  Text(
                                    'Daily Steps',
                                    style: AppTypography.bodySm.copyWith(
                                      color: AppColors.onSurfaceVariant,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ],
                              ),
                              AppSpacing.gapVMd,
                              Text(
                                steps.currentValue,
                                style: AppTypography.headlineLgMobile.copyWith(
                                  color: AppColors.onSurface,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              AppSpacing.gapVXs,
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: AppColors.primaryFixedDim.withValues(alpha: 0.3),
                                  borderRadius: AppRadius.radiusXs,
                                ),
                                child: Text(
                                  steps.statusLabel,
                                  style: AppTypography.labelSm.copyWith(
                                    color: AppColors.primary,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      AppSpacing.gapHMd,

                      // Heart Rate Card
                      Expanded(
                        child: AppCard(
                          padding: const EdgeInsets.all(AppSpacing.md),
                          onTap: () => context.push(AppRoutes.healthTracker),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.favorite_rounded, size: 20, color: AppColors.error),
                                  AppSpacing.gapHXs,
                                  Text(
                                    'Heart Rate',
                                    style: AppTypography.bodySm.copyWith(
                                      color: AppColors.onSurfaceVariant,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ],
                              ),
                              AppSpacing.gapVMd,
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.baseline,
                                textBaseline: TextBaseline.alphabetic,
                                children: [
                                  Text(
                                    heartRate.currentValue,
                                    style: AppTypography.headlineLgMobile.copyWith(
                                      color: AppColors.onSurface,
                                      fontWeight: FontWeight.w800,
                                    ),
                                  ),
                                  AppSpacing.gapHXs,
                                  Text(
                                    'bpm',
                                    style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                                  ),
                                ],
                              ),
                              AppSpacing.gapVXs,
                              Text(
                                heartRate.statusLabel,
                                style: AppTypography.labelSm.copyWith(
                                  color: AppColors.onSurfaceVariant,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  );
                },
                loading: () => const AppCard(child: AppLoading()),
                error: (_, __) => const SizedBox.shrink(),
              ),
              AppSpacing.gapV2Xl,

              // 6. Recommended Top Doctors
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Top Specialists',
                    style: AppTypography.headlineSm.copyWith(
                      color: AppColors.onSurface,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  TextButton(
                    onPressed: () => context.push(AppRoutes.doctorSearch),
                    child: Text(
                      'View all',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ],
              ),
              AppSpacing.gapVSm,
              recommendedDocsAsync.when(
                data: (doctors) {
                  return ListView.separated(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: doctors.length,
                    separatorBuilder: (_, __) => AppSpacing.gapVMd,
                    itemBuilder: (context, index) {
                      return DoctorCard(doctor: doctors[index]);
                    },
                  );
                },
                loading: () => const AppLoading(message: 'Loading specialists...'),
                error: (err, _) => const AppError(message: 'Failed to load doctors'),
              ),
              AppSpacing.gapV2Xl,
            ],
          ),
        ),
      ),
    );
  }
}

class _QuickActionTile extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color iconBgColor;
  final Color iconColor;
  final VoidCallback onTap;

  const _QuickActionTile({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.iconBgColor,
    required this.iconColor,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return AppCard(
      onTap: onTap,
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: iconBgColor,
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: iconColor, size: 22),
          ),
          AppSpacing.gapVSm,
          Text(
            title,
            style: AppTypography.titleMedium.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
          Text(
            subtitle,
            style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}
