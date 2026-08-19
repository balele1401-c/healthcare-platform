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
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../doctor/domain/models/doctor_model.dart';
import '../../domain/models/appointment_model.dart';
import '../controllers/appointment_controller.dart';

class SelectDateTimeScreen extends ConsumerStatefulWidget {
  final DoctorModel doctor;

  const SelectDateTimeScreen({
    super.key,
    required this.doctor,
  });

  @override
  ConsumerState<SelectDateTimeScreen> createState() => _SelectDateTimeScreenState();
}

class _SelectDateTimeScreenState extends ConsumerState<SelectDateTimeScreen> {
  late DateTime _selectedDate;
  String? _selectedTimeSlot;
  ConsultationType _consultationType = ConsultationType.inPerson;
  final TextEditingController _notesController = TextEditingController();

  final List<String> _morningSlots = ['09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', '11:00 AM'];
  final List<String> _afternoonSlots = ['01:00 PM', '01:30 PM', '02:00 PM', '02:30 PM', '03:30 PM', '04:00 PM'];

  @override
  void initState() {
    super.initState();
    _selectedDate = DateTime.now().add(const Duration(days: 1));
    _selectedTimeSlot = '10:00 AM';
  }

  @override
  void dispose() {
    _notesController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Generate next 14 calendar dates
    final dates = List.generate(14, (i) => DateTime.now().add(Duration(days: i + 1)));

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
          'Select Date & Time',
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
            // 1. Doctor Header Summary
            AppCard(
              child: Row(
                children: [
                  AppAvatar(
                    name: widget.doctor.name,
                    imageUrl: widget.doctor.avatarUrl,
                    size: 50,
                  ),
                  AppSpacing.gapHMd,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          widget.doctor.name,
                          style: AppTypography.titleMedium.copyWith(
                            color: AppColors.onSurface,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        Text(
                          widget.doctor.specialty,
                          style: AppTypography.bodySm.copyWith(color: AppColors.primary),
                        ),
                        Text(
                          widget.doctor.clinicName,
                          style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            AppSpacing.gapVLg,

            // 2. Consultation Type
            Text(
              'Consultation Type',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            Row(
              children: [
                Expanded(
                  child: _ConsultationTypeCard(
                    title: 'In-Person Visit',
                    subtitle: 'At clinic location',
                    icon: Icons.local_hospital_outlined,
                    isSelected: _consultationType == ConsultationType.inPerson,
                    onTap: () {
                      setState(() {
                        _consultationType = ConsultationType.inPerson;
                      });
                    },
                  ),
                ),
                AppSpacing.gapHMd,
                Expanded(
                  child: _ConsultationTypeCard(
                    title: 'Video Call',
                    subtitle: 'Online teleconsult',
                    icon: Icons.videocam_outlined,
                    isSelected: _consultationType == ConsultationType.videoCall,
                    onTap: () {
                      setState(() {
                        _consultationType = ConsultationType.videoCall;
                      });
                    },
                  ),
                ),
              ],
            ),
            AppSpacing.gapVLg,

            // 3. Select Date (Horizontal Scrollable Strip)
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  'Select Date',
                  style: AppTypography.headlineSm.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                Text(
                  DateFormat('MMMM yyyy').format(_selectedDate),
                  style: AppTypography.labelMd.copyWith(
                    color: AppColors.primary,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
            AppSpacing.gapVSm,
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: dates.map((date) {
                  final isSelected = DateUtils.isSameDay(_selectedDate, date);
                  return Padding(
                    padding: const EdgeInsets.only(right: AppSpacing.sm),
                    child: GestureDetector(
                      onTap: () {
                        setState(() {
                          _selectedDate = date;
                        });
                      },
                      child: Container(
                        width: 64,
                        padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
                        decoration: BoxDecoration(
                          color: isSelected ? AppColors.primary : AppColors.surfaceContainerLowest,
                          borderRadius: AppRadius.radiusMd,
                          border: Border.all(
                            color: isSelected ? AppColors.primary : AppColors.outlineVariant,
                            width: isSelected ? 2 : 1,
                          ),
                          boxShadow: isSelected
                              ? [
                                  BoxShadow(
                                    color: AppColors.primary.withOpacity(0.25),
                                    blurRadius: 8,
                                    offset: const Offset(0, 4),
                                  ),
                                ]
                              : null,
                        ),
                        child: Column(
                          children: [
                            Text(
                              DateFormat('E').format(date),
                              style: AppTypography.labelSm.copyWith(
                                color: isSelected ? AppColors.onPrimary.withOpacity(0.9) : AppColors.onSurfaceVariant,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            AppSpacing.gapVXs,
                            Text(
                              DateFormat('d').format(date),
                              style: AppTypography.titleLarge.copyWith(
                                color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  );
                }).toList(),
              ),
            ),
            AppSpacing.gapVLg,

            // 4. Select Time Slots
            Text(
              'Available Time Slots',
              style: AppTypography.headlineSm.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            Text(
              'Morning Slots',
              style: AppTypography.labelMd.copyWith(
                color: AppColors.onSurfaceVariant,
                fontWeight: FontWeight.w600,
              ),
            ),
            AppSpacing.gapVSm,
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: _morningSlots.map((slot) {
                final isSelected = _selectedTimeSlot == slot;
                return _TimeSlotChip(
                  time: slot,
                  isSelected: isSelected,
                  onTap: () {
                    setState(() {
                      _selectedTimeSlot = slot;
                    });
                  },
                );
              }).toList(),
            ),
            AppSpacing.gapVMd,
            Text(
              'Afternoon Slots',
              style: AppTypography.labelMd.copyWith(
                color: AppColors.onSurfaceVariant,
                fontWeight: FontWeight.w600,
              ),
            ),
            AppSpacing.gapVSm,
            Wrap(
              spacing: AppSpacing.sm,
              runSpacing: AppSpacing.sm,
              children: _afternoonSlots.map((slot) {
                final isSelected = _selectedTimeSlot == slot;
                return _TimeSlotChip(
                  time: slot,
                  isSelected: isSelected,
                  onTap: () {
                    setState(() {
                      _selectedTimeSlot = slot;
                    });
                  },
                );
              }).toList(),
            ),
            AppSpacing.gapVLg,

            // 5. Patient Notes (Optional)
            Text(
              'Appointment Notes (Optional)',
              style: AppTypography.titleMedium.copyWith(
                color: AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            AppSpacing.gapVSm,
            TextField(
              controller: _notesController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'Briefly describe your symptoms or reason for visit...',
                filled: true,
                fillColor: AppColors.surfaceContainerLowest,
                border: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.outlineVariant),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.outlineVariant),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: AppRadius.radiusMd,
                  borderSide: const BorderSide(color: AppColors.primary, width: 2),
                ),
              ),
            ),
            AppSpacing.gapV2Xl,
          ],
        ),
      ),
      bottomNavigationBar: Container(
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
          child: AppButton(
            text: 'Continue to Confirmation',
            isDisabled: _selectedTimeSlot == null,
            onPressed: () {
              if (_selectedTimeSlot == null) {
                AppSnackbar.showError(context, 'Please select a time slot.');
                return;
              }

              final draft = ref.read(bookingDraftProvider.notifier);
              draft.setDoctor(widget.doctor);
              draft.setDate(_selectedDate);
              draft.setTimeSlot(_selectedTimeSlot!);
              draft.setConsultationType(_consultationType);
              draft.setPatientNotes(_notesController.text.trim());

              context.push(AppRoutes.appointmentConfirmation);
            },
          ),
        ),
      ),
    );
  }
}

class _ConsultationTypeCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final bool isSelected;
  final VoidCallback onTap;

  const _ConsultationTypeCard({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.primaryFixedDim.withOpacity(0.2) : AppColors.surfaceContainerLowest,
          borderRadius: AppRadius.radiusMd,
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.outlineVariant,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(
              icon,
              size: 24,
              color: isSelected ? AppColors.primary : AppColors.onSurfaceVariant,
            ),
            AppSpacing.gapVSm,
            Text(
              title,
              style: AppTypography.bodyMd.copyWith(
                color: isSelected ? AppColors.primary : AppColors.onSurface,
                fontWeight: FontWeight.w700,
              ),
            ),
            Text(
              subtitle,
              style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
            ),
          ],
        ),
      ),
    );
  }
}

class _TimeSlotChip extends StatelessWidget {
  final String time;
  final bool isSelected;
  final VoidCallback onTap;

  const _TimeSlotChip({
    required this.time,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.primary : AppColors.surfaceContainerLowest,
          borderRadius: AppRadius.radiusBase,
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.outlineVariant,
            width: isSelected ? 2 : 1,
          ),
        ),
        child: Text(
          time,
          style: AppTypography.labelMd.copyWith(
            color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
            fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
          ),
        ),
      ),
    );
  }
}
