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
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_skeleton.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/appointment_model.dart';
import '../controllers/appointment_controller.dart';

class MyAppointmentsScreen extends ConsumerWidget {
  const MyAppointmentsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return DefaultTabController(
      length: 3,
      child: Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(
          backgroundColor: AppColors.surface,
          elevation: 0,
          scrolledUnderElevation: 0.5,
          leading: Navigator.canPop(context)
              ? IconButton(
                  icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
                  onPressed: () => Navigator.pop(context),
                )
              : null,
          title: Text(
            'My Clinical Visits',
            style: AppTypography.titleLarge.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w800,
            ),
          ),
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(48),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 4),
              color: AppColors.surface,
              child: TabBar(
                indicatorColor: AppColors.primary,
                indicatorWeight: 3,
                labelColor: AppColors.primary,
                unselectedLabelColor: AppColors.onSurfaceVariant,
                labelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w700, fontSize: 14),
                unselectedLabelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w500, fontSize: 14),
                tabs: const [
                  Tab(text: 'Upcoming'),
                  Tab(text: 'Completed'),
                  Tab(text: 'Cancelled'),
                ],
              ),
            ),
          ),
        ),
        body: const TabBarView(
          children: [
            _AppointmentTabList(status: AppointmentStatus.upcoming),
            _AppointmentTabList(status: AppointmentStatus.completed),
            _AppointmentTabList(status: AppointmentStatus.cancelled),
          ],
        ),
      ),
    );
  }
}

class _AppointmentTabList extends ConsumerWidget {
  final AppointmentStatus status;

  const _AppointmentTabList({required this.status});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final appointmentsAsync = status == AppointmentStatus.upcoming
        ? ref.watch(upcomingAppointmentsProvider)
        : status == AppointmentStatus.completed
            ? ref.watch(completedAppointmentsProvider)
            : ref.watch(cancelledAppointmentsProvider);

    return LayoutBuilder(
      builder: (context, constraints) {
        final isDesktop = constraints.maxWidth >= 900;

        return appointmentsAsync.when(
          data: (appointments) {
            if (appointments.isEmpty) {
              return Center(
                child: AppEmptyState(
                  icon: Icons.calendar_today_outlined,
                  title: 'No ${status.label} Visits',
                  message: status == AppointmentStatus.upcoming
                      ? 'You do not have any upcoming doctor appointments scheduled.'
                      : 'No ${status.label.toLowerCase()} appointment records found.',
                  actionText: status == AppointmentStatus.upcoming ? 'Book a Specialist' : null,
                  onAction: status == AppointmentStatus.upcoming ? () => context.push(AppRoutes.doctorSearch) : null,
                ),
              );
            }

            return RefreshIndicator(
              onRefresh: () async {
                ref.invalidate(upcomingAppointmentsProvider);
                ref.invalidate(completedAppointmentsProvider);
                ref.invalidate(cancelledAppointmentsProvider);
              },
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 860),
                  child: ListView.separated(
                    padding: EdgeInsets.symmetric(
                      horizontal: isDesktop ? AppSpacing.desktopMargin : AppSpacing.marginMobile,
                      vertical: AppSpacing.lg,
                    ),
                    itemCount: appointments.length,
                    separatorBuilder: (context, index) => AppSpacing.gapVMd,
                    itemBuilder: (context, index) {
                      final apt = appointments[index];
                      return _AppointmentItemCard(appointment: apt);
                    },
                  ),
                ),
              ),
            );
          },
          loading: () => Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: ListView.separated(
                padding: const EdgeInsets.all(AppSpacing.marginMobile),
                itemCount: 3,
                separatorBuilder: (context, index) => AppSpacing.gapVMd,
                itemBuilder: (context, index) => const AppSkeleton(width: double.infinity, height: 160),
              ),
            ),
          ),
          error: (err, _) => Center(
            child: AppError(
              message: 'Failed to load appointments.',
              onRetry: () {
                ref.invalidate(upcomingAppointmentsProvider);
                ref.invalidate(completedAppointmentsProvider);
                ref.invalidate(cancelledAppointmentsProvider);
              },
            ),
          ),
        );
      },
    );
  }
}

class _AppointmentItemCard extends ConsumerWidget {
  final AppointmentModel appointment;

  const _AppointmentItemCard({required this.appointment});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dateFormatted = DateFormat('EEEE, MMM d, yyyy').format(appointment.dateTime);

    return AppCard(
      onTap: () => context.push(AppRoutes.appointmentDetail, extra: appointment),
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Doctor Header & Status Pill
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
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
                    ),
                    AppSpacing.gapVXs,
                    Text(
                      appointment.doctorSpecialty,
                      style: AppTypography.bodySm.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    Text(
                      appointment.clinicName,
                      style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              AppBadge(
                text: appointment.status.label,
                variant: appointment.status == AppointmentStatus.upcoming
                    ? BadgeVariant.primary
                    : appointment.status == AppointmentStatus.completed
                        ? BadgeVariant.success
                        : BadgeVariant.error,
              ),
            ],
          ),
          AppSpacing.gapVMd,

          // Schedule Banner
          Container(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 8),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: AppRadius.radiusMd,
              border: Border.all(color: AppColors.outlineVariant, width: 0.8),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.calendar_today_rounded, size: 15, color: AppColors.primary),
                    AppSpacing.gapHSm,
                    Text(
                      dateFormatted,
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
                    const Icon(Icons.access_time_rounded, size: 15, color: AppColors.primary),
                    AppSpacing.gapHSm,
                    Text(
                      appointment.timeSlot,
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

          // Actions Row
          Row(
            children: [
              Expanded(
                child: AppButton(
                  text: 'View Details',
                  variant: ButtonVariant.outlined,
                  height: 42,
                  onPressed: () => context.push(AppRoutes.appointmentDetail, extra: appointment),
                ),
              ),
              if (appointment.status == AppointmentStatus.upcoming) ...[
                AppSpacing.gapHMd,
                Expanded(
                  child: AppButton(
                    text: appointment.consultationType == ConsultationType.videoCall ? 'Join Consultation' : 'Check In',
                    height: 42,
                    prefixIcon: appointment.consultationType == ConsultationType.videoCall ? Icons.video_call_rounded : Icons.check_circle_outline_rounded,
                    onPressed: () {
                      if (appointment.consultationType == ConsultationType.videoCall) {
                        AppSnackbar.showSuccess(context, 'Joining video room with ${appointment.doctorName}...');
                      } else {
                        context.push(AppRoutes.appointmentDetail, extra: appointment);
                      }
                    },
                  ),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }
}
