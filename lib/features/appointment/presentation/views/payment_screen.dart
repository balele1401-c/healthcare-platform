import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
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
      'subtitle': 'Visa, Mastercard, Amex (•••• 4242)',
      'icon': Icons.credit_card_rounded,
      'badge': 'Instant',
    },
    {
      'id': 'va',
      'title': 'Bank Virtual Account',
      'subtitle': 'BCA, Mandiri, BNI, BRI, Chase',
      'icon': Icons.account_balance_rounded,
      'badge': 'Auto-Verify',
    },
    {
      'id': 'ewallet',
      'title': 'E-Wallet / Instant Pay',
      'subtitle': 'Apple Pay, Google Pay, QRIS',
      'icon': Icons.qr_code_scanner_rounded,
      'badge': 'Fastest',
    },
    {
      'id': 'transfer',
      'title': 'Manual Bank Transfer',
      'subtitle': 'Direct deposit with proof of payment',
      'icon': Icons.receipt_long_rounded,
      'badge': null,
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
        AppSnackbar.showError(context, 'Payment failed. Please try again.');
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
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Payment & Checkout',
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
            // 0. Sandbox / Readiness Transparency Banner
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(AppSpacing.md),
              margin: const EdgeInsets.only(bottom: AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.primaryContainer.withValues(alpha: 0.25),
                borderRadius: AppRadius.radiusMd,
                border: Border.all(
                  color: AppColors.primary.withValues(alpha: 0.3),
                ),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Icon(Icons.info_outline_rounded, color: AppColors.primary, size: 20),
                  AppSpacing.gapHSm,
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Demonstration & Sandbox Mode',
                          style: AppTypography.titleMd.copyWith(
                            color: AppColors.primary,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          'Payment gateway integration is operating in sandbox readiness mode pending financial institution onboarding. No live funds will be debited.',
                          style: AppTypography.bodySm.copyWith(
                            color: AppColors.onSurfaceVariant,
                            height: 1.3,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),

            // 1. Amount Payable Card
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(AppSpacing.lg),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [AppColors.primary, Color(0xFF003FA4)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: AppRadius.radiusLg,
                boxShadow: [
                  BoxShadow(
                    color: AppColors.primary.withValues(alpha: 0.3),
                    blurRadius: 12,
                    offset: const Offset(0, 6),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Total Payment Due',
                    style: AppTypography.bodySm.copyWith(color: AppColors.onPrimary.withValues(alpha: 0.85)),
                  ),
                  AppSpacing.gapVSm,
                  Text(
                    '\$${totalAmount.toStringAsFixed(2)}',
                    style: AppTypography.displayLarge.copyWith(
                      color: AppColors.onPrimary,
                      fontWeight: FontWeight.w800,
                      fontSize: 34,
                    ),
                  ),
                  AppSpacing.gapVSm,
                  Row(
                    children: [
                      const Icon(Icons.verified_user_rounded, color: AppColors.onPrimary, size: 16),
                      AppSpacing.gapHXs,
                      Text(
                        'Secure 256-Bit Encrypted Healthcare Checkout',
                        style: AppTypography.labelSm.copyWith(color: AppColors.onPrimary.withValues(alpha: 0.9)),
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
              style: AppTypography.headlineSm.copyWith(
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
                    hasBorder: true,
                    borderRadius: AppRadius.radiusMd,
                    backgroundColor: isSelected ? AppColors.surfaceContainerLow : AppColors.surfaceContainerLowest,
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(AppSpacing.sm),
                          decoration: BoxDecoration(
                            color: isSelected ? AppColors.primaryFixed : AppColors.surfaceContainerHigh,
                            borderRadius: AppRadius.radiusSm,
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
                                    ),
                                  ),
                                  if (method['badge'] != null) ...[
                                    AppSpacing.gapHSm,
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: AppColors.primaryFixedDim.withValues(alpha: 0.3),
                                        borderRadius: AppRadius.radiusXs,
                                      ),
                                      child: Text(
                                        method['badge'] as String,
                                        style: AppTypography.labelSm.copyWith(
                                          color: AppColors.primary,
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ],
                                ],
                              ),
                              AppSpacing.gapVXs,
                              Text(
                                method['subtitle'] as String,
                                style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                              ),
                            ],
                          ),
                        ),
                        Icon(
                          isSelected ? Icons.radio_button_checked_rounded : Icons.radio_button_off_rounded,
                          color: isSelected ? AppColors.primary : AppColors.onSurfaceVariant,
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
            AppSpacing.gapVLg,

            // 3. Security Notice
            Container(
              padding: const EdgeInsets.all(AppSpacing.md),
              decoration: BoxDecoration(
                color: AppColors.surfaceContainerLow,
                borderRadius: AppRadius.radiusMd,
              ),
              child: Row(
                children: [
                  const Icon(Icons.shield_outlined, color: AppColors.primary, size: 22),
                  AppSpacing.gapHMd,
                  Expanded(
                    child: Text(
                      'Your clinical consultation and payment details are strictly HIPAA compliant and protected.',
                      style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                    ),
                  ),
                ],
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
              color: Colors.black.withValues(alpha: 0.05),
              offset: const Offset(0, -4),
              blurRadius: 10,
            ),
          ],
        ),
        child: SafeArea(
          child: AppButton(
            text: 'Pay Now \$${totalAmount.toStringAsFixed(2)}',
            isLoading: _isProcessing,
            prefixIcon: Icons.payment_rounded,
            onPressed: _handlePayment,
          ),
        ),
      ),
    );
  }
}
