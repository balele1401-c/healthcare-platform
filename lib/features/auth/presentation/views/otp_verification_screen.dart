import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_header.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../controllers/auth_controller.dart';

class OtpVerificationScreen extends ConsumerStatefulWidget {
  const OtpVerificationScreen({super.key});

  @override
  ConsumerState<OtpVerificationScreen> createState() => _OtpVerificationScreenState();
}

class _OtpVerificationScreenState extends ConsumerState<OtpVerificationScreen> {
  final List<TextEditingController> _controllers = List.generate(6, (_) => TextEditingController());
  final List<FocusNode> _focusNodes = List.generate(6, (_) => FocusNode());

  Timer? _timer;
  int _secondsRemaining = 60;
  bool _canResend = false;

  @override
  void initState() {
    super.initState();
    _startCountdown();
  }

  void _startCountdown() {
    setState(() {
      _secondsRemaining = 60;
      _canResend = false;
    });

    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_secondsRemaining > 0) {
        setState(() {
          _secondsRemaining--;
        });
      } else {
        setState(() {
          _canResend = true;
        });
        timer.cancel();
      }
    });
  }

  String get _enteredOtp {
    return _controllers.map((c) => c.text).join();
  }

  Future<void> _handleVerify() async {
    final otp = _enteredOtp;
    if (otp.length < 6) {
      AppSnackbar.showError(context, 'Please enter the complete 6-digit code.');
      return;
    }

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.verifyOtp(otpCode: otp);

    if (!mounted) return;

    if (success) {
      AppSnackbar.showSuccess(context, 'Verification successful!');
      final user = ref.read(authControllerProvider).user;
      if (user != null && !user.isHealthProfileCompleted) {
        context.go(AppRoutes.createHealthProfile);
      } else {
        context.go(AppRoutes.home);
      }
    } else {
      final errorMsg = ref.read(authControllerProvider).errorMessage ?? 'Invalid OTP code.';
      AppSnackbar.showError(context, errorMsg);
    }
  }

  Future<void> _handleResend() async {
    if (!_canResend) return;

    final identifier = ref.read(authControllerProvider).otpIdentifier ?? 'user@example.com';
    final authController = ref.read(authControllerProvider.notifier);
    await authController.sendForgotPasswordOtp(identifier: identifier);

    if (!mounted) return;

    AppSnackbar.showSuccess(context, 'A new verification code has been sent.');
    _startCountdown();
  }

  @override
  void dispose() {
    _timer?.cancel();
    for (final c in _controllers) {
      c.dispose();
    }
    for (final f in _focusNodes) {
      f.dispose();
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);
    final identifier = authState.otpIdentifier ?? 'your registered account';

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: const AppHeader(title: 'OTP Verification'),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: AppSpacing.paddingScreenAll,
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 440),
              child: AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Container(
                      width: 56,
                      height: 56,
                      decoration: const BoxDecoration(
                        color: AppColors.secondaryContainer,
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.mark_email_read_rounded,
                        color: AppColors.secondary,
                        size: 30,
                      ),
                    ),
                    AppSpacing.gapVMd,
                    Text(
                      'Enter Verification Code',
                      style: AppTypography.headlineSm.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w700,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    AppSpacing.gapVXs,
                    Text(
                      'We have sent a 6-digit verification code to:\n$identifier',
                      style: AppTypography.bodySm.copyWith(
                        color: AppColors.onSurfaceVariant,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    AppSpacing.gapVXl,

                    // 6-Digit Box Row
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: List.generate(6, (index) {
                        return SizedBox(
                          width: 44,
                          height: 54,
                          child: TextFormField(
                            controller: _controllers[index],
                            focusNode: _focusNodes[index],
                            keyboardType: TextInputType.number,
                            textAlign: TextAlign.center,
                            maxLength: 1,
                            style: AppTypography.headlineSm.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w700,
                            ),
                            inputFormatters: [
                              FilteringTextInputFormatter.digitsOnly,
                            ],
                            decoration: InputDecoration(
                              counterText: '',
                              contentPadding: EdgeInsets.zero,
                              filled: true,
                              fillColor: AppColors.surfaceContainerLowest,
                              border: OutlineInputBorder(
                                borderRadius: AppRadius.radiusBase,
                                borderSide: const BorderSide(color: AppColors.outlineVariant),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: AppRadius.radiusBase,
                                borderSide: const BorderSide(color: AppColors.primary, width: 2),
                              ),
                            ),
                            onChanged: (value) {
                              if (value.isNotEmpty && index < 5) {
                                _focusNodes[index + 1].requestFocus();
                              } else if (value.isEmpty && index > 0) {
                                _focusNodes[index - 1].requestFocus();
                              }
                              if (_enteredOtp.length == 6) {
                                _handleVerify();
                              }
                            },
                          ),
                        );
                      }),
                    ),
                    AppSpacing.gapVLg,

                    // Resend Timer Info
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        if (!_canResend) ...[
                          const Icon(Icons.timer_outlined, size: 16, color: AppColors.outline),
                          AppSpacing.gapHXs,
                          Text(
                            'Resend code in 00:${_secondsRemaining.toString().padLeft(2, '0')}',
                            style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                          ),
                        ] else ...[
                          Text(
                            "Didn't receive the code? ",
                            style: AppTypography.bodySm.copyWith(color: AppColors.onSurfaceVariant),
                          ),
                          AppTextButton(
                            text: 'Resend Code',
                            onPressed: _handleResend,
                          ),
                        ],
                      ],
                    ),
                    AppSpacing.gapVXl,

                    // Verify Button
                    AppButton(
                      text: 'Verify & Proceed',
                      isLoading: authState.isLoading,
                      onPressed: _handleVerify,
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
