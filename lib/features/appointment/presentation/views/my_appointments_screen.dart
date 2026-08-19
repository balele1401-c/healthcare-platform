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
import '../../../../shared/widgets/app_empty_state.dart';
import '../../../../shared/widgets/app_error.dart';
import '../../../../shared/widgets/app_loading.dart';
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
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
            onPressed: () => Navigator.pop(context),
          ),
          title: Text(
            'My Appointments',
            style: AppTypography.titleLarge.copyWith(
              color: AppColors.onSurface,
              fontWeight: FontWeight.w700,
            ),
          ),
          bottom: TabBar(
            indicatorColor: AppColors.primary,
            indicatorWeight: 3,
            labelColor: AppColors.primary,
            unselectedLabelColor: AppColors.onSurfaceVariant,
            labelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w700),
            unselectedLabelStyle: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w500),
            tabs: const [
              Tab(text: 'Upcoming'),
              Tab(text: 'Completed'),
              Tab(text: 'Cancelled'),
            ],
          ),
        ),
        body: TabBarView(
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

    return appointmentsAsync.when(
      data: (appointments) {
        if (appointments.isEmpty) {
          return AppEmptyState(
            icon: Icons.calendar_today_outlined,
            title: 'No ${status.label} Appointments',
            message: status == AppointmentStatus.upcoming
                ? 'You do not have any upcoming visits scheduled.'
                : 'No ${status.label.toLowerCase()} appointments on record.',
            actionText: status == AppointmentStatus.upcoming ? 'Book Appointment' : null,
            onAction: status == AppointmentStatus.upcoming ? () => context.push(AppRoutes.doctorSearch) : null,
          );
        }

        return RefreshIndicator(
          onRefresh: () async {
            ref.invalidate(upcomingAppointmentsProvider);
            ref.invalidate(completedAppointmentsProvider);
            ref.invalidate(cancelledAppointmentsProvider);
          },
          child: ListView.separated(
            padding: const EdgeInsets.all(AppSpacing.marginMobile),
            itemCount: appointments.length,
            separatorBuilder: (_, __) => AppSpacing.gapVMd,
            itemBuilder: (context, index) {
              final apt = appointments[index];
              return _AppointmentItemCard(appointment: apt);
            },
          ),
        );
      },
      loading: () => const Center(child: AppLoading(message: 'Loading appointments...')),
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
  }
}

class _AppointmentItemCard extends ConsumerWidget {
  final AppointmentModel appointment;

  const _AppointmentItemCard({required this.appointment});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dateFormatted = DateFormat('EEE, MMM d, yyyy').format(appointment.dateTime);

    return AppCard(
      onTap: () => context.push(AppRoutes.appointmentDetail, extra: appointment),
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
                    Text(
                      appointment.doctorSpecialty,
                      style: AppTypography.bodySm.copyWith(color: AppColors.primary),
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
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainerLow,
              borderRadius: AppRadius.radiusMd,
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    const Icon(Icons.calendar_today_rounded, size: 16, color: AppColors.primary),
                    AppSpacing.gapHXs,
                    Text(
                      dateFormatted,
                      style: AppTypography.bodySm.copyWith(fontWeight: FontWeight.w600),
                    ),
                  ],
                ),
                Container(width: 1, height: 16, color: AppColors.outlineVariant),
                Row(
                  children: [
                    const Icon(Icons.access_time_rounded, size: 16, color: AppColors.primary),
                    AppSpacing.gapHXs,
                    Text(
                      appointment.timeSlot,
                      style: AppTypography.bodySm.copyWith(fontWeight: FontWeight.w600),
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
                child: OutlinedButton(
                  style: OutlinedButton.styleFrom(
                    foregroundColor: AppColors.primary,
                    side: const BorderSide(color: AppColors.outlineVariant),
                    shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusBase),
                    padding: const EdgeInsets.symmetric(vertical: 10),
                  ),
                  onPressed: () => context.push(AppRoutes.appointmentDetail, extra: appointment),
                  child: Text(
                    'View Details',
                    style: AppTypography.labelMd.copyWith(fontWeight: FontWeight.w700),
                  ),
                ),
              ),
              if (appointment.status == AppointmentStatus.upcoming) ...[
                AppSpacing.gapHMd,
                Expanded(
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      foregroundColor: AppColors.onPrimary,
                      shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusBase),
                      padding: const EdgeInsets.symmetric(vertical: 10),
                    ),
                    onPressed: () {
                      if (appointment.consultationType == ConsultationType.videoCall) {
                        AppSnackbar.showSuccess(context, 'Joining video room with ${appointment.doctorName}...');
                      } else {
                        context.push(AppRoutes.appointmentDetail, extra: appointment);
                      }
                    },
                    child: Text(
                      appointment.consultationType == ConsultationType.videoCall ? 'Join Call' : 'Check In',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.onPrimary,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
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
