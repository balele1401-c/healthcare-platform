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
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_dialog.dart';
import '../../../../shared/widgets/app_divider.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../domain/models/appointment_model.dart';
import '../controllers/appointment_controller.dart';

class AppointmentDetailScreen extends ConsumerStatefulWidget {
  final AppointmentModel appointment;

  const AppointmentDetailScreen({
    super.key,
    required this.appointment,
  });

  @override
  ConsumerState<AppointmentDetailScreen> createState() => _AppointmentDetailScreenState();
}

class _AppointmentDetailScreenState extends ConsumerState<AppointmentDetailScreen> {
  late AppointmentModel _currentAppointment;

  @override
  void initState() {
    super.initState();
    _currentAppointment = widget.appointment;
  }

  void _handleCancel() async {
    final confirmed = await AppDialog.showConfirmDialog(
      context: context,
      title: 'Cancel Appointment?',
      message:
          'Are you sure you want to cancel your appointment with ${_currentAppointment.doctorName}? A full refund will be credited according to clinic policy.',
      confirmText: 'Yes, Cancel',
      cancelText: 'Keep Visit',
      isDestructive: true,
    );

    if (confirmed == true && mounted) {
      final repo = ref.read(appointmentRepositoryProvider);
      await repo.cancelAppointment(_currentAppointment.id);
      ref.invalidate(upcomingAppointmentsProvider);
      ref.invalidate(cancelledAppointmentsProvider);
      ref.invalidate(nextAppointmentProvider);

      setState(() {
        _currentAppointment = _currentAppointment.copyWith(status: AppointmentStatus.cancelled);
      });
      if (mounted) {
        AppSnackbar.showInfo(context, 'Appointment ${_currentAppointment.id} has been cancelled.');
      }
    }
  }

  void _handleReschedule() async {
    final newDate = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 2)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 30)),
    );

    if (newDate != null && mounted) {
      final repo = ref.read(appointmentRepositoryProvider);
      await repo.rescheduleAppointment(_currentAppointment.id, newDate, '02:00 PM');
      ref.invalidate(upcomingAppointmentsProvider);
      ref.invalidate(nextAppointmentProvider);

      if (mounted) {
        setState(() {
          _currentAppointment = _currentAppointment.copyWith(
            dateTime: newDate,
            timeSlot: '02:00 PM',
            status: AppointmentStatus.upcoming,
          );
        });
        AppSnackbar.showSuccess(context, 'Rescheduled to ${DateFormat('MMM d, yyyy').format(newDate)} at 02:00 PM.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final dateFormatted = DateFormat('EEEE, MMMM d, yyyy').format(_currentAppointment.dateTime);

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
          'Appointment Details',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.chat_bubble_outline_rounded, color: AppColors.primary),
            onPressed: () => context.push(AppRoutes.chat),
          ),
        ],
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Status & ID Card
            AppCard(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Appointment ID',
                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      AppSpacing.gapVXs,
                      Text(
                        _currentAppointment.id,
                        style: AppTypography.titleMedium.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                  AppBadge(
                    text: _currentAppointment.status.label,
                    variant: _currentAppointment.status == AppointmentStatus.upcoming
                        ? BadgeVariant.primary
                        : _currentAppointment.status == AppointmentStatus.completed
                            ? BadgeVariant.success
                            : BadgeVariant.error,
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Doctor Info
            Text(
              'Doctor Information',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                children: [
                  Row(
                    children: [
                      AppAvatar(
                        name: _currentAppointment.doctorName,
                        imageUrl: _currentAppointment.doctorAvatarUrl,
                        size: 60,
                      ),
                      AppSpacing.gapHMd,
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              _currentAppointment.doctorName,
                              style: AppTypography.titleMedium.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            Text(
                              _currentAppointment.doctorSpecialty,
                              style: AppTypography.bodySm.copyWith(color: AppColors.primary),
                            ),
                            AppSpacing.gapVXs,
                            Text(
                              _currentAppointment.clinicName,
                              style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 24),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          icon: const Icon(Icons.chat_outlined, size: 18),
                          label: const Text('Direct Chat'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppColors.primary,
                            side: const BorderSide(color: AppColors.outlineVariant),
                            shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusBase),
                          ),
                          onPressed: () => context.push(AppRoutes.chat),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 3. Schedule & Mode
            Text(
              'Schedule & Location',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                children: [
                  _DetailLine(
                    icon: Icons.calendar_today_rounded,
                    label: 'Date',
                    value: dateFormatted,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 20),
                  _DetailLine(
                    icon: Icons.access_time_rounded,
                    label: 'Time Slot',
                    value: _currentAppointment.timeSlot,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 20),
                  _DetailLine(
                    icon: _currentAppointment.consultationType.icon,
                    label: 'Type',
                    value: _currentAppointment.consultationType.label,
                  ),
                  const Divider(color: AppColors.outlineVariant, height: 20),
                  _DetailLine(
                    icon: Icons.location_on_outlined,
                    label: 'Location',
                    value: '${_currentAppointment.clinicName}\n${_currentAppointment.clinicAddress}',
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Clinical Diagnosis & Patient Notes (if present)
            if (_currentAppointment.diagnosisSummary != null || _currentAppointment.patientNotes != null) ...[
              Text(
                'Clinical Notes',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVSm,
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (_currentAppointment.patientNotes != null) ...[
                      Text(
                        'Patient Reason for Visit',
                        style: AppTypography.labelSm.copyWith(
                          color: AppColors.onSurfaceVariant,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      AppSpacing.gapVXs,
                      Text(
                        _currentAppointment.patientNotes!,
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                      ),
                    ],
                    if (_currentAppointment.diagnosisSummary != null) ...[
                      if (_currentAppointment.patientNotes != null)
                        const Divider(color: AppColors.outlineVariant, height: 20),
                      Text(
                        'Physician Summary',
                        style: AppTypography.labelSm.copyWith(
                          color: AppColors.onSurfaceVariant,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      AppSpacing.gapVXs,
                      Text(
                        _currentAppointment.diagnosisSummary!,
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                      ),
                    ],
                  ],
                ),
              ),
              AppSpacing.gapVLg,
            ],

            // 5. Payment Invoice Receipt
            Text(
              'Payment Receipt',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            AppCard(
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Consultation Fee', style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant)),
                      Text('\$${_currentAppointment.consultationFee.toStringAsFixed(2)}', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    ],
                  ),
                  AppSpacing.gapVSm,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Platform Service Fee', style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant)),
                      Text('\$${_currentAppointment.serviceFee.toStringAsFixed(2)}', style: AppTypography.bodyMd.copyWith(fontWeight: FontWeight.w600)),
                    ],
                  ),
                  const AppDivider(),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Total Paid', style: AppTypography.titleMedium.copyWith(fontWeight: FontWeight.w800)),
                      Text(
                        '\$${_currentAppointment.totalAmount.toStringAsFixed(2)}',
                        style: AppTypography.headlineSm.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapVSm,
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Payment Method', style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant)),
                      Text(_currentAppointment.paymentMethod, style: AppTypography.labelSm.copyWith(fontWeight: FontWeight.w600)),
                    ],
                  ),
                ],
              ),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
      bottomNavigationBar: _currentAppointment.status == AppointmentStatus.upcoming
          ? Container(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.marginMobile,
                vertical: AppSpacing.md,
              ),
              decoration: BoxDecoration(
                color: AppColors.surface,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    offset: const Offset(0, -4),
                    blurRadius: 10,
                  ),
                ],
              ),
              child: SafeArea(
                child: Row(
                  children: [
                    Expanded(
                      child: AppButton(
                        text: 'Reschedule',
                        variant: ButtonVariant.outlined,
                        onPressed: _handleReschedule,
                      ),
                    ),
                    AppSpacing.gapHMd,
                    Expanded(
                      child: AppButton(
                        text: 'Cancel',
                        variant: ButtonVariant.error,
                        onPressed: _handleCancel,
                      ),
                    ),
                  ],
                ),
              ),
            )
          : null,
    );
  }
}

class _DetailLine extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _DetailLine({
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
