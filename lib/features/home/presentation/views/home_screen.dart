import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_metric_card.dart';
import '../../../../shared/widgets/app_skeleton.dart';
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
        scrolledUnderElevation: 0.5,
        titleSpacing: AppSpacing.md,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(AppSpacing.xs + 2),
              decoration: BoxDecoration(
                color: AppColors.primaryContainer,
                borderRadius: AppRadius.radiusMd,
                border: Border.all(color: AppColors.primaryFixedDim, width: 0.8),
              ),
              child: const Icon(
                Icons.health_and_safety_rounded,
                color: AppColors.primary,
                size: 20,
              ),
            ),
            AppSpacing.gapHSm,
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'HealthCare',
                  style: AppTypography.titleMedium.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                Text(
                  'Clinical Patient Portal',
                  style: AppTypography.labelSm.copyWith(
                    color: AppColors.onSurfaceVariant,
                    fontSize: 10,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          // Live Clinical Status Pill
          Container(
            margin: const EdgeInsets.symmetric(vertical: 12),
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: AppColors.successContainer,
              borderRadius: AppRadius.radiusFull,
              border: Border.all(color: const Color(0xFFA7F3D0), width: 0.8),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 6,
                  height: 6,
                  decoration: const BoxDecoration(
                    color: AppColors.success,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 6),
                Text(
                  'System Active',
                  style: AppTypography.labelSm.copyWith(
                    color: AppColors.onSuccessContainer,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          AppSpacing.gapHSm,

          // Notifications Action with Badge
          IconButton(
            icon: Stack(
              clipBehavior: Clip.none,
              children: [
                const Icon(Icons.notifications_none_rounded, color: AppColors.onSurface, size: 24),
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
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),
                        )
                      : const SizedBox.shrink(),
                  loading: () => const SizedBox.shrink(),
                  error: (err, stack) => const SizedBox.shrink(),
                ),
              ],
            ),
            onPressed: () => context.push(AppRoutes.notifications),
          ),

          Padding(
            padding: const EdgeInsets.only(right: AppSpacing.md, left: AppSpacing.xs),
            child: GestureDetector(
              onTap: () => context.push(AppRoutes.profile),
              child: Container(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  border: Border.all(color: AppColors.outlineVariant, width: 1.5),
                ),
                child: AppAvatar(
                  name: profile.fullName,
                  imageUrl: profile.avatarUrl,
                  size: 34,
                ),
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
        child: LayoutBuilder(
          builder: (context, constraints) {
            final isDesktop = constraints.maxWidth >= 900;
            final isTablet = constraints.maxWidth >= 600 && constraints.maxWidth < 900;

            return SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: EdgeInsets.symmetric(
                horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                vertical: AppSpacing.lg,
              ),
              child: isDesktop
                  ? _buildDesktopLayout(
                      context,
                      ref,
                      profile,
                      nextAppointmentAsync,
                      recommendedDocsAsync,
                      healthMetricsAsync,
                    )
                  : _buildMobileLayout(
                      context,
                      ref,
                      profile,
                      nextAppointmentAsync,
                      recommendedDocsAsync,
                      healthMetricsAsync,
                      isTablet,
                    ),
            );
          },
        ),
      ),
    );
  }

  // DESKTOP TWO-COLUMN RESPONSIVE LAYOUT
  Widget _buildDesktopLayout(
    BuildContext context,
    WidgetRef ref,
    dynamic profile,
    AsyncValue<dynamic> nextAppointmentAsync,
    AsyncValue<dynamic> recommendedDocsAsync,
    AsyncValue<dynamic> healthMetricsAsync,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Top Welcome Header Strip
        _buildWelcomeHeader(context, profile),
        AppSpacing.gapVXl,

        // 4 Key Health Metrics
        _buildHealthMetricsGrid(context, healthMetricsAsync, crossAxisCount: 4),
        AppSpacing.gapVXl,

        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Left Column: Upcoming Appointment & Top Doctors
            Expanded(
              flex: 3,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildUpcomingAppointmentSection(context, nextAppointmentAsync),
                  AppSpacing.gapVXl,
                  _buildRecommendedDoctorsSection(context, recommendedDocsAsync),
                ],
              ),
            ),
            AppSpacing.gapHXl,

            // Right Column: Quick Operations & AI Clinical Assistant
            Expanded(
              flex: 2,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _buildQuickActionsSection(context, crossAxisCount: 2),
                  AppSpacing.gapVXl,
                  _buildAIAssistantHeroCard(context),
                ],
              ),
            ),
          ],
        ),
      ],
    );
  }

  // MOBILE & TABLET LAYOUT
  Widget _buildMobileLayout(
    BuildContext context,
    WidgetRef ref,
    dynamic profile,
    AsyncValue<dynamic> nextAppointmentAsync,
    AsyncValue<dynamic> recommendedDocsAsync,
    AsyncValue<dynamic> healthMetricsAsync,
    bool isTablet,
  ) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildWelcomeHeader(context, profile),
        AppSpacing.gapVLg,

        // Quick Search Trigger
        _buildSearchBarTrigger(context),
        AppSpacing.gapVLg,

        // Upcoming Appointment Spotlight
        _buildUpcomingAppointmentSection(context, nextAppointmentAsync),
        AppSpacing.gapVXl,

        // Health Vitals Overview
        _buildHealthMetricsGrid(context, healthMetricsAsync, crossAxisCount: isTablet ? 4 : 2),
        AppSpacing.gapVXl,

        // Quick Actions
        _buildQuickActionsSection(context, crossAxisCount: isTablet ? 3 : 2),
        AppSpacing.gapVXl,

        // Top Doctors
        _buildRecommendedDoctorsSection(context, recommendedDocsAsync),
        AppSpacing.gapVXl,
      ],
    );
  }

  // WELCOME HEADER
  Widget _buildWelcomeHeader(BuildContext context, dynamic profile) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '${_getGreeting()}, ${profile.fullName.split(' ').first}',
                style: AppTypography.headlineMd.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.5,
                ),
              ),
              AppSpacing.gapVXs,
              Text(
                'Here is your clinical schedule, vital trends, and specialized care portal.',
                style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // SEARCH BAR TRIGGER
  Widget _buildSearchBarTrigger(BuildContext context) {
    return GestureDetector(
      onTap: () => context.push(AppRoutes.doctorSearch),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 12),
        decoration: BoxDecoration(
          color: AppColors.surface,
          borderRadius: AppRadius.radiusMd,
          border: Border.all(color: AppColors.outlineVariant, width: 0.8),
          boxShadow: AppShadows.cardAmbient,
        ),
        child: Row(
          children: [
            const Icon(Icons.search_rounded, color: AppColors.onSurfaceVariant, size: 20),
            AppSpacing.gapHSm,
            Expanded(
              child: Text(
                'Search doctor name, medical specialty, or clinic...',
                style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceMuted),
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: AppColors.surfaceContainerLow,
                borderRadius: AppRadius.radiusSm,
                border: Border.all(color: AppColors.outlineVariant, width: 0.8),
              ),
              child: Row(
                children: [
                  const Icon(Icons.tune_rounded, size: 14, color: AppColors.primary),
                  const SizedBox(width: 4),
                  Text(
                    'Filters',
                    style: AppTypography.labelSm.copyWith(
                      color: AppColors.primary,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // UPCOMING APPOINTMENT SECTION
  Widget _buildUpcomingAppointmentSection(BuildContext context, AsyncValue<dynamic> nextAppointmentAsync) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.event_available_rounded, size: 20, color: AppColors.primary),
                AppSpacing.gapHSm,
                Text(
                  'Upcoming Consultation',
                  style: AppTypography.titleLarge.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
            TextButton(
              onPressed: () => context.push(AppRoutes.myAppointments),
              child: Text(
                'All Bookings →',
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
                padding: const EdgeInsets.all(AppSpacing.xl),
                child: AppEmptyState(
                  icon: Icons.calendar_today_outlined,
                  title: 'No Appointments Scheduled',
                  message: 'Schedule a virtual or in-clinic visit with our verified healthcare specialists.',
                  actionText: 'Book a Doctor',
                  onAction: () => context.push(AppRoutes.doctorSearch),
                ),
              );
            }

            return Container(
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: AppRadius.radiusLg,
                border: Border.all(color: AppColors.primaryFixedDim, width: 1),
                boxShadow: AppShadows.elevated,
              ),
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Top Status Header
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      AppBadge(
                        text: 'Confirmed Visit',
                        variant: BadgeVariant.primary,
                        icon: Icons.verified_rounded,
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLow,
                          borderRadius: AppRadius.radiusSm,
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.schedule_rounded, size: 14, color: AppColors.onSurfaceVariant),
                            const SizedBox(width: 4),
                            Text(
                              appointment.timeSlot,
                              style: AppTypography.labelSm.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapVMd,

                  // Doctor Profile Row
                  Row(
                    children: [
                      AppAvatar(
                        name: appointment.doctorName,
                        imageUrl: appointment.doctorAvatarUrl,
                        size: 52,
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
                              style: AppTypography.bodySm.copyWith(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                        decoration: BoxDecoration(
                          color: AppColors.secondaryContainer,
                          borderRadius: AppRadius.radiusMd,
                          border: Border.all(color: AppColors.secondaryFixedDim, width: 0.8),
                        ),
                        child: Row(
                          children: [
                            Icon(appointment.consultationType.icon, size: 16, color: AppColors.onSecondaryContainer),
                            const SizedBox(width: 6),
                            Text(
                              appointment.consultationType.label,
                              style: AppTypography.labelSm.copyWith(
                                color: AppColors.onSecondaryContainer,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapVLg,

                  // Action Buttons
                  Row(
                    children: [
                      Expanded(
                        flex: 2,
                        child: ElevatedButton.icon(
                          onPressed: () => context.push(
                            AppRoutes.appointmentDetail,
                            extra: appointment,
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppColors.primary,
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: const RoundedRectangleBorder(
                              borderRadius: AppRadius.radiusBase,
                            ),
                          ),
                          icon: const Icon(Icons.video_call_rounded, size: 20),
                          label: Text(
                            'Join Consultation Room',
                            style: AppTypography.labelMd.copyWith(
                              color: Colors.white,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                      AppSpacing.gapHMd,
                      Expanded(
                        flex: 1,
                        child: OutlinedButton(
                          onPressed: () => context.push(
                            AppRoutes.appointmentDetail,
                            extra: appointment,
                          ),
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: const RoundedRectangleBorder(
                              borderRadius: AppRadius.radiusBase,
                            ),
                            side: const BorderSide(color: AppColors.outlineVariant, width: 1),
                          ),
                          child: Text(
                            'Details',
                            style: AppTypography.labelMd.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            );
          },
          loading: () => const AppSkeleton(width: double.infinity, height: 180),
          error: (err, _) => const AppCard(child: AppError(message: 'Could not load upcoming appointment')),
        ),
      ],
    );
  }

  // HEALTH METRICS GRID
  Widget _buildHealthMetricsGrid(
    BuildContext context,
    AsyncValue<dynamic> healthMetricsAsync, {
    int? crossAxisCount,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.monitor_heart_outlined, size: 20, color: AppColors.secondary),
                AppSpacing.gapHSm,
                Text(
                  'Daily Vitals & Health Trends',
                  style: AppTypography.titleLarge.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
            TextButton(
              onPressed: () => context.push(AppRoutes.healthTracker),
              child: Text(
                'Full Tracker →',
                style: AppTypography.labelMd.copyWith(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ],
        ),
        AppSpacing.gapVSm,
        LayoutBuilder(
          builder: (context, constraints) {
            final width = constraints.maxWidth;
            final int cols;
            final double ratio;

            if (width >= 1100) {
              cols = 4;
              ratio = 1.35;
            } else if (width >= 750) {
              cols = 2;
              ratio = 1.55;
            } else if (width >= 420) {
              cols = 2;
              ratio = 1.15;
            } else {
              cols = 1;
              ratio = 2.3;
            }

            return healthMetricsAsync.when(
              data: (metrics) {
                return GridView.count(
                  crossAxisCount: cols,
                  crossAxisSpacing: AppSpacing.md,
                  mainAxisSpacing: AppSpacing.md,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  childAspectRatio: ratio,
                  children: [
                    AppMetricCard(
                      title: 'Heart Rate',
                      value: '72',
                      unit: 'BPM',
                      icon: Icons.favorite_rounded,
                      accentColor: AppColors.error,
                      status: 'Resting',
                      statusVariant: BadgeVariant.success,
                      trend: '+2% vs avg',
                      onTap: () => context.push(AppRoutes.healthTracker),
                    ),
                    AppMetricCard(
                      title: 'Blood Pressure',
                      value: '120/80',
                      unit: 'mmHg',
                      icon: Icons.speed_rounded,
                      accentColor: AppColors.primary,
                      status: 'Optimal',
                      statusVariant: BadgeVariant.primary,
                      trend: 'Stable',
                      onTap: () => context.push(AppRoutes.healthTracker),
                    ),
                    AppMetricCard(
                      title: 'Daily Steps',
                      value: '8,420',
                      unit: 'steps',
                      icon: Icons.directions_walk_rounded,
                      accentColor: AppColors.secondary,
                      status: '84% Goal',
                      statusVariant: BadgeVariant.secondary,
                      trend: '+12% vs yday',
                      onTap: () => context.push(AppRoutes.healthTracker),
                    ),
                    AppMetricCard(
                      title: 'Sleep Duration',
                      value: '7.5',
                      unit: 'hrs',
                      icon: Icons.bedtime_rounded,
                      accentColor: const Color(0xFF6366F1),
                      status: 'Deep 88%',
                      statusVariant: BadgeVariant.info,
                      trend: '+30m rested',
                      onTap: () => context.push(AppRoutes.healthTracker),
                    ),
                  ],
                );
              },
              loading: () => GridView.count(
                crossAxisCount: cols,
                crossAxisSpacing: AppSpacing.md,
                mainAxisSpacing: AppSpacing.md,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                childAspectRatio: ratio,
                children: List.generate(
                  cols,
                  (index) => const AppSkeleton(width: double.infinity, height: 120),
                ),
              ),
              error: (err, stack) => const SizedBox.shrink(),
            );
          },
        ),
      ],
    );
  }

  // QUICK ACTIONS SECTION
  Widget _buildQuickActionsSection(BuildContext context, {required int crossAxisCount}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            const Icon(Icons.bolt_rounded, size: 20, color: AppColors.warning),
            AppSpacing.gapHSm,
            Text(
              'Patient Services',
              style: AppTypography.titleLarge.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
          ],
        ),
        AppSpacing.gapVSm,
        GridView.count(
          crossAxisCount: crossAxisCount,
          crossAxisSpacing: AppSpacing.md,
          mainAxisSpacing: AppSpacing.md,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          childAspectRatio: 1.35,
          children: [
            _QuickActionTile(
              title: 'Find Doctors',
              subtitle: 'Top specialists & clinics',
              icon: Icons.search_rounded,
              iconBgColor: AppColors.primaryContainer,
              iconColor: AppColors.primary,
              onTap: () => context.push(AppRoutes.doctorSearch),
            ),
            _QuickActionTile(
              title: 'Medical Records',
              subtitle: 'Lab results & diagnoses',
              icon: Icons.receipt_long_rounded,
              iconBgColor: AppColors.secondaryContainer,
              iconColor: AppColors.secondary,
              onTap: () => context.push(AppRoutes.medicalRecords),
            ),
            _QuickActionTile(
              title: 'Prescriptions',
              subtitle: 'Active drugs & refills',
              icon: Icons.medication_rounded,
              iconBgColor: AppColors.successContainer,
              iconColor: AppColors.success,
              onTap: () => context.push(AppRoutes.prescriptions),
            ),
            _QuickActionTile(
              title: 'AI Health Guide',
              subtitle: '24/7 symptom triage',
              icon: Icons.auto_awesome_rounded,
              iconBgColor: const Color(0xFFFAF5FF),
              iconColor: const Color(0xFF9333EA),
              onTap: () => context.push(AppRoutes.aiAssistant),
            ),
          ],
        ),
      ],
    );
  }

  // AI ASSISTANT HERO CARD
  Widget _buildAIAssistantHeroCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF1E3A8A), Color(0xFF1E40AF)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: AppRadius.radiusLg,
        boxShadow: AppShadows.elevated,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: AppRadius.radiusMd,
                ),
                child: const Icon(Icons.auto_awesome_rounded, color: Colors.white, size: 20),
              ),
              AppSpacing.gapHSm,
              Text(
                'AI Clinical Assistant',
                style: AppTypography.titleMedium.copyWith(
                  color: Colors.white,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          AppSpacing.gapVMd,
          Text(
            'Ask medical questions, analyze symptoms, or prepare for your upcoming doctor consultation.',
            style: AppTypography.bodySm.copyWith(
              color: Colors.white.withValues(alpha: 0.85),
              height: 1.4,
            ),
          ),
          AppSpacing.gapVLg,
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () => context.push(AppRoutes.aiAssistant),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: AppColors.primary,
                elevation: 0,
                padding: const EdgeInsets.symmetric(vertical: 12),
                shape: const RoundedRectangleBorder(
                  borderRadius: AppRadius.radiusBase,
                ),
              ),
              child: Text(
                'Start Clinical Chat →',
                style: AppTypography.labelMd.copyWith(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // TOP RECOMMENDED DOCTORS SECTION
  Widget _buildRecommendedDoctorsSection(BuildContext context, AsyncValue<dynamic> recommendedDocsAsync) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                const Icon(Icons.stars_rounded, size: 20, color: Color(0xFFF59E0B)),
                AppSpacing.gapHSm,
                Text(
                  'Top Medical Specialists',
                  style: AppTypography.titleLarge.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
            TextButton(
              onPressed: () => context.push(AppRoutes.doctorSearch),
              child: Text(
                'Browse All →',
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
              separatorBuilder: (context, index) => AppSpacing.gapVMd,
              itemBuilder: (context, index) {
                return DoctorCard(doctor: doctors[index]);
              },
            );
          },
          loading: () => ListView.separated(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: 3,
            separatorBuilder: (context, index) => AppSpacing.gapVMd,
            itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 120),
          ),
          error: (err, _) => const AppError(message: 'Failed to load doctors'),
        ),
      ],
    );
  }
}

class _QuickActionTile extends StatefulWidget {
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
  State<_QuickActionTile> createState() => _QuickActionTileState();
}

class _QuickActionTileState extends State<_QuickActionTile> {
  bool _isHovering = false;

  @override
  Widget build(BuildContext context) {
    return MouseRegion(
      onEnter: (_) => setState(() => _isHovering = true),
      onExit: (_) => setState(() => _isHovering = false),
      child: AnimatedScale(
        scale: _isHovering ? 1.02 : 1.0,
        duration: const Duration(milliseconds: 150),
        curve: Curves.easeOutCubic,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 150),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: AppRadius.radiusLg,
            border: Border.all(
              color: _isHovering ? widget.iconColor.withValues(alpha: 0.5) : AppColors.outlineVariant,
              width: 0.8,
            ),
            boxShadow: _isHovering ? AppShadows.cardHover : AppShadows.cardAmbient,
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              onTap: widget.onTap,
              borderRadius: AppRadius.radiusLg,
              child: Padding(
                padding: const EdgeInsets.all(AppSpacing.md),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Container(
                      width: 38,
                      height: 38,
                      decoration: BoxDecoration(
                        color: widget.iconBgColor,
                        borderRadius: AppRadius.radiusMd,
                      ),
                      child: Icon(widget.icon, color: widget.iconColor, size: 20),
                    ),
                    AppSpacing.gapVSm,
                    Text(
                      widget.title,
                      style: AppTypography.titleMedium.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    AppSpacing.gapVXs,
                    Text(
                      widget.subtitle,
                      style: AppTypography.labelSm.copyWith(
                        color: AppColors.onSurfaceVariant,
                        fontSize: 11,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
