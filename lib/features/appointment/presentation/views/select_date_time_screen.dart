import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
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
        scrolledUnderElevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Select Schedule & Mode',
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
                    // 1. Doctor Header Summary
                    AppCard(
                      padding: const EdgeInsets.all(AppSpacing.md),
                      child: Row(
                        children: [
                          AppAvatar(
                            name: widget.doctor.name,
                            imageUrl: widget.doctor.avatarUrl,
                            size: 52,
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
                                AppSpacing.gapVXs,
                                Text(
                                  widget.doctor.specialty,
                                  style: AppTypography.bodySm.copyWith(
                                    color: AppColors.primary,
                                    fontWeight: FontWeight.w600,
                                  ),
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
                          AppBadge(
                            text: '\$${widget.doctor.consultationFee.toStringAsFixed(0)}',
                            variant: BadgeVariant.primary,
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 2. Consultation Type
                    Text(
                      'Consultation Mode',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    Row(
                      children: [
                        Expanded(
                          child: _ConsultationTypeCard(
                            title: 'In-Clinic Visit',
                            subtitle: 'Physical consultation at clinic',
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
                            title: 'HD Video Consultation',
                            subtitle: 'Secure telemedicine room',
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
                          style: AppTypography.titleLarge.copyWith(
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
                              child: AnimatedContainer(
                                duration: const Duration(milliseconds: 150),
                                width: 68,
                                padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
                                decoration: BoxDecoration(
                                  color: isSelected ? AppColors.primary : AppColors.surface,
                                  borderRadius: AppRadius.radiusMd,
                                  border: Border.all(
                                    color: isSelected ? AppColors.primary : AppColors.outlineVariant,
                                    width: isSelected ? 1.5 : 0.8,
                                  ),
                                  boxShadow: isSelected ? AppShadows.cardHover : AppShadows.cardAmbient,
                                ),
                                child: Column(
                                  children: [
                                    Text(
                                      DateFormat('EEE').format(date).toUpperCase(),
                                      style: AppTypography.labelSm.copyWith(
                                        color: isSelected ? Colors.white.withValues(alpha: 0.9) : AppColors.onSurfaceVariant,
                                        fontWeight: FontWeight.w600,
                                        fontSize: 11,
                                      ),
                                    ),
                                    AppSpacing.gapVXs,
                                    Text(
                                      DateFormat('d').format(date),
                                      style: AppTypography.titleLarge.copyWith(
                                        color: isSelected ? Colors.white : AppColors.onSurface,
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
                      'Available Appointment Slots',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    Text(
                      'Morning Sessions',
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
                      'Afternoon Sessions',
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

                    // 5. Patient Notes / Symptoms (Optional)
                    Text(
                      'Symptoms / Clinical Notes (Optional)',
                      style: AppTypography.titleLarge.copyWith(
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
                        fillColor: AppColors.surface,
                        border: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.outlineVariant, width: 0.8),
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: AppRadius.radiusMd,
                          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
                        ),
                      ),
                    ),
                    AppSpacing.gapVXxl,
                  ],
                ),
              ),
            ),
          );
        },
      ),

      // 6. Sticky Bottom Action Bar
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        decoration: const BoxDecoration(
          color: AppColors.surface,
          border: Border(
            top: BorderSide(color: AppColors.outlineVariant, width: 0.8),
          ),
          boxShadow: AppShadows.bottomNav,
        ),
        child: SafeArea(
          child: Center(
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 860),
              child: Row(
                children: [
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Total Payable',
                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      Text(
                        '\$${widget.doctor.consultationFee.toStringAsFixed(0)}',
                        style: AppTypography.headlineSm.copyWith(
                          color: AppColors.onSurface,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                  AppSpacing.gapHLg,
                  Expanded(
                    child: AppButton(
                      text: 'Review & Confirm Booking',
                      prefixIcon: Icons.arrow_forward_rounded,
                      onPressed: _selectedTimeSlot == null
                          ? null
                          : () {
                              final draftNotifier = ref.read(bookingDraftProvider.notifier);
                              draftNotifier.setDate(_selectedDate);
                              draftNotifier.setTimeSlot(_selectedTimeSlot!);
                              draftNotifier.setConsultationType(_consultationType);
                              draftNotifier.setPatientNotes(_notesController.text.trim());

                              context.push(AppRoutes.appointmentConfirmation);
                            },
                    ),
                  ),
                ],
              ),
            ),
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
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.primaryContainer.withValues(alpha: 0.5) : AppColors.surface,
          borderRadius: AppRadius.radiusMd,
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.outlineVariant,
            width: isSelected ? 1.5 : 0.8,
          ),
          boxShadow: isSelected ? AppShadows.cardHover : AppShadows.cardAmbient,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: isSelected ? AppColors.primary : AppColors.surfaceContainerLow,
                borderRadius: AppRadius.radiusSm,
              ),
              child: Icon(
                icon,
                color: isSelected ? Colors.white : AppColors.primary,
                size: 22,
              ),
            ),
            AppSpacing.gapVMd,
            Text(
              title,
              style: AppTypography.titleMedium.copyWith(
                color: isSelected ? AppColors.primary : AppColors.onSurface,
                fontWeight: FontWeight.w700,
                fontSize: 14,
              ),
            ),
            AppSpacing.gapVXs,
            Text(
              subtitle,
              style: AppTypography.labelSm.copyWith(
                color: AppColors.onSurfaceVariant,
                fontSize: 11,
              ),
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
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
        decoration: BoxDecoration(
          color: isSelected ? AppColors.primary : AppColors.surface,
          borderRadius: AppRadius.radiusBase,
          border: Border.all(
            color: isSelected ? AppColors.primary : AppColors.outlineVariant,
            width: isSelected ? 1.5 : 0.8,
          ),
        ),
        child: Text(
          time,
          style: AppTypography.labelMd.copyWith(
            color: isSelected ? Colors.white : AppColors.onSurface,
            fontWeight: isSelected ? FontWeight.w700 : FontWeight.w500,
          ),
        ),
      ),
    );
  }
}
