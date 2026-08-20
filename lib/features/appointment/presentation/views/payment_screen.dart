import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../controllers/appointment_controller.dart';

class PaymentScreen extends ConsumerStatefulWidget {
  const PaymentScreen({super.key});

  @override
  ConsumerState<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends ConsumerState<PaymentScreen> {
  String _selectedMethod = 'card';
  bool _isProcessing = false;

  final List<Map<String, dynamic>> _paymentMethods = [
    {
      'id': 'card',
      'title': 'Credit / Debit Card',
      'subtitle': 'Visa, Mastercard, American Express',
      'icon': Icons.credit_card_rounded,
      'badge': 'Instant',
    },
    {
      'id': 'va',
      'title': 'Bank Virtual Account',
      'subtitle': 'Automatic clinical settlement',
      'icon': Icons.account_balance_rounded,
      'badge': 'Automated',
    },
    {
      'id': 'ewallet',
      'title': 'Apple Pay / Google Pay / QRIS',
      'subtitle': 'One-tap biometric checkout',
      'icon': Icons.qr_code_scanner_rounded,
      'badge': 'Fastest',
    },
    {
      'id': 'insurance',
      'title': 'Health Insurance Direct Billing',
      'subtitle': 'Pre-approved corporate policy claims',
      'icon': Icons.health_and_safety_rounded,
      'badge': 'Direct Claim',
    },
  ];

  Future<void> _handlePayment() async {
    setState(() {
      _isProcessing = true;
    });

    final draft = ref.read(bookingDraftProvider);
    final doctor = draft.doctor;

    if (doctor == null) {
      context.go(AppRoutes.home);
      return;
    }

    try {
      final repository = ref.read(appointmentRepositoryProvider);
      final newAppointment = await repository.createAppointment(
        doctorId: doctor.id,
        doctorName: doctor.name,
        doctorSpecialty: doctor.specialty,
        doctorAvatarUrl: doctor.avatarUrl,
        clinicName: doctor.clinicName,
        clinicAddress: doctor.clinicAddress,
        dateTime: draft.selectedDate ?? DateTime.now().add(const Duration(days: 1)),
        timeSlot: draft.selectedTimeSlot ?? '10:00 AM',
        consultationType: draft.consultationType,
        consultationFee: doctor.consultationFee,
        paymentMethod: _paymentMethods.firstWhere((m) => m['id'] == _selectedMethod)['title'],
        patientNotes: draft.patientNotes,
      );

      // Invalidate appointment providers
      ref.invalidate(upcomingAppointmentsProvider);
      ref.invalidate(nextAppointmentProvider);

      if (mounted) {
        context.pushReplacement(AppRoutes.appointmentSuccess, extra: newAppointment);
      }
    } catch (e) {
      if (mounted) {
        setState(() {
          _isProcessing = false;
        });
        AppSnackbar.showError(context, 'Payment processing failed. Please try again.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final draft = ref.watch(bookingDraftProvider);
    final doctor = draft.doctor;
    final double totalAmount = (doctor?.consultationFee ?? 75.0) + 5.0;

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
          'Secure Checkout',
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
                    // 1. Amount Payable Hero Card
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(AppSpacing.xl),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF0F172A), Color(0xFF1E3A8A)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: AppRadius.radiusLg,
                        boxShadow: AppShadows.elevated,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'TOTAL HEALTHCARE CONSULTATION DUE',
                            style: AppTypography.labelSm.copyWith(
                              color: Colors.white.withValues(alpha: 0.8),
                              fontWeight: FontWeight.w700,
                              letterSpacing: 0.8,
                            ),
                          ),
                          AppSpacing.gapVSm,
                          Text(
                            '\$${totalAmount.toStringAsFixed(2)}',
                            style: AppTypography.displayLarge.copyWith(
                              color: Colors.white,
                              fontWeight: FontWeight.w800,
                              fontSize: 38,
                            ),
                          ),
                          AppSpacing.gapVMd,
                          Row(
                            children: [
                              const Icon(Icons.lock_rounded, color: AppColors.secondaryLight, size: 16),
                              AppSpacing.gapHXs,
                              Text(
                                '256-Bit SSL Encrypted Healthcare Payment Gateway',
                                style: AppTypography.labelSm.copyWith(
                                  color: Colors.white.withValues(alpha: 0.9),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                    AppSpacing.gapVLg,

                    // 2. Payment Method Selector
                    Text(
                      'Select Payment Method',
                      style: AppTypography.titleLarge.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    Column(
                      children: _paymentMethods.map((method) {
                        final isSelected = _selectedMethod == method['id'];
                        return Padding(
                          padding: const EdgeInsets.only(bottom: AppSpacing.md),
                          child: AppCard(
                            onTap: () {
                              setState(() {
                                _selectedMethod = method['id'];
                              });
                            },
                            padding: const EdgeInsets.all(AppSpacing.md),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: isSelected ? AppColors.primaryContainer : AppColors.surfaceContainerLow,
                                    borderRadius: AppRadius.radiusMd,
                                  ),
                                  child: Icon(
                                    method['icon'] as IconData,
                                    color: isSelected ? AppColors.primary : AppColors.onSurfaceVariant,
                                    size: 24,
                                  ),
                                ),
                                AppSpacing.gapHMd,
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Row(
                                        children: [
                                          Text(
                                            method['title'] as String,
                                            style: AppTypography.titleMedium.copyWith(
                                              color: AppColors.onSurface,
                                              fontWeight: FontWeight.w700,
                                              fontSize: 15,
                                            ),
                                          ),
                                          if (method['badge'] != null) ...[
                                            const SizedBox(width: 8),
                                            AppBadge(
                                              text: method['badge'] as String,
                                              variant: BadgeVariant.success,
                                            ),
                                          ],
                                        ],
                                      ),
                                      AppSpacing.gapVXs,
                                      Text(
                                        method['subtitle'] as String,
                                        style: AppTypography.labelSm.copyWith(color: AppColors.onSurfaceVariant),
                                      ),
                                    ],
                                  ),
                                ),
                                Container(
                                  width: 22,
                                  height: 22,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: isSelected ? AppColors.primary : Colors.transparent,
                                    border: Border.all(
                                      color: isSelected ? AppColors.primary : AppColors.outline,
                                      width: isSelected ? 0 : 1.5,
                                    ),
                                  ),
                                  child: isSelected
                                      ? const Center(
                                          child: Icon(Icons.check_rounded, color: Colors.white, size: 14),
                                        )
                                      : null,
                                ),
                              ],
                            ),
                          ),
                        );
                      }).toList(),
                    ),
                    AppSpacing.gapVXxl,
                  ],
                ),
              ),
            ),
          );
        },
      ),

      // 3. Fixed Bottom Pay Bar
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
              child: AppButton(
                text: 'Pay \$${totalAmount.toStringAsFixed(2)} & Confirm',
                isLoading: _isProcessing,
                prefixIcon: Icons.lock_outline_rounded,
                onPressed: _isProcessing ? null : _handlePayment,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
