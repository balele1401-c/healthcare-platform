import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../core/utils/validators.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_header.dart';
import '../../../../shared/widgets/app_password_field.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../controllers/auth_controller.dart';

class PatientRegisterScreen extends ConsumerStatefulWidget {
  const PatientRegisterScreen({super.key});

  @override
  ConsumerState<PatientRegisterScreen> createState() => _PatientRegisterScreenState();
}

class _PatientRegisterScreenState extends ConsumerState<PatientRegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  bool _acceptTerms = false;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    super.dispose();
  }

  Future<void> _handleRegister() async {
    if (!_formKey.currentState!.validate()) return;

    if (!_acceptTerms) {
      AppSnackbar.showError(context, 'Please accept the Terms & Privacy Policy to register.');
      return;
    }

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phoneNumber: _phoneController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;

    if (success) {
      AppSnackbar.showSuccess(context, 'Account created! Please set up your health profile.');
      context.go(AppRoutes.createHealthProfile);
    } else {
      final errorMessage = ref.read(authControllerProvider).errorMessage ?? 'Registration failed.';
      AppSnackbar.showError(context, errorMessage);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: const AppHeader(title: 'Registration'),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: AppSpacing.paddingScreenAll,
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 440),
              child: Column(
                children: [
                  AppCard(
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Create Account',
                            style: AppTypography.headlineSm.copyWith(
                              color: AppColors.onSurface,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          AppSpacing.gapVXs,
                          Text(
                            'Join HealthCare for seamless digital medical access.',
                            style: AppTypography.bodySm.copyWith(
                              color: AppColors.onSurfaceVariant,
                            ),
                          ),
                          AppSpacing.gapVLg,

                          // Name
                          AppTextField(
                            label: 'Full Name',
                            hintText: 'Sarah Jenkins',
                            controller: _nameController,
                            validator: Validators.validateName,
                            prefixIcon: const Icon(Icons.person_outline_rounded, size: 20, color: AppColors.outline),
                          ),
                          AppSpacing.gapVMd,

                          // Email
                          AppTextField(
                            label: 'Email Address',
                            hintText: 'name@example.com',
                            controller: _emailController,
                            keyboardType: TextInputType.emailAddress,
                            validator: Validators.validateEmail,
                            prefixIcon: const Icon(Icons.mail_outline_rounded, size: 20, color: AppColors.outline),
                          ),
                          AppSpacing.gapVMd,

                          // Phone
                          AppTextField(
                            label: 'Phone Number',
                            hintText: '+1 555-019-2834',
                            controller: _phoneController,
                            keyboardType: TextInputType.phone,
                            validator: Validators.validatePhone,
                            prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.outline),
                          ),
                          AppSpacing.gapVMd,

                          // Password
                          AppPasswordField(
                            label: 'Password',
                            controller: _passwordController,
                            validator: Validators.validatePassword,
                          ),
                          AppSpacing.gapVMd,

                          // Confirm Password
                          AppPasswordField(
                            label: 'Confirm Password',
                            controller: _confirmPasswordController,
                            validator: (val) => Validators.validateConfirmPassword(_passwordController.text, val),
                          ),
                          AppSpacing.gapVMd,

                          // Terms Checkbox
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Checkbox(
                                value: _acceptTerms,
                                onChanged: (val) {
                                  setState(() {
                                    _acceptTerms = val ?? false;
                                  });
                                },
                              ),
                              Expanded(
                                child: Padding(
                                  padding: const EdgeInsets.only(top: 12),
                                  child: Text(
                                    'I agree to the HealthCare Terms of Service and Privacy Policy.',
                                    style: AppTypography.bodySm.copyWith(
                                      color: AppColors.onSurfaceVariant,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          AppSpacing.gapVLg,

                          // Register CTA
                          AppButton(
                            text: 'Create Account',
                            isLoading: authState.isLoading,
                            onPressed: _handleRegister,
                          ),
                        ],
                      ),
                    ),
                  ),
                  AppSpacing.gapVLg,

                  // Login Link
                  Wrap(
                    alignment: WrapAlignment.center,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    children: [
                      Text(
                        'Already have an account? ',
                        style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
                      ),
                      AppTextButton(
                        text: 'Sign In',
                        onPressed: () => context.pop(),
                      ),
                    ],
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
