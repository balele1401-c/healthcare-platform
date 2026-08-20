import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../core/utils/date_formatter.dart';
import '../../../../core/utils/validators.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_header.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';
import '../../domain/models/health_profile_model.dart';
import '../controllers/auth_controller.dart';

class CreateHealthProfileScreen extends ConsumerStatefulWidget {
  const CreateHealthProfileScreen({super.key});

  @override
  ConsumerState<CreateHealthProfileScreen> createState() => _CreateHealthProfileScreenState();
}

class _CreateHealthProfileScreenState extends ConsumerState<CreateHealthProfileScreen> {
  final _formKey = GlobalKey<FormState>();

  DateTime? _selectedDob = DateTime(1995, 6, 15);
  String _selectedGender = 'female';
  String _selectedBloodType = 'O+';
  final _heightController = TextEditingController(text: '168');
  final _weightController = TextEditingController(text: '62');
  final _emergencyNameController = TextEditingController(text: 'David Jenkins');
  final _emergencyPhoneController = TextEditingController(text: '+1 555-019-9876');

  final List<String> _bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final user = ref.read(authControllerProvider).user;
      if (user != null) {
        ref.read(patientProfileProvider.notifier).syncFromUser(user);
      }
    });
  }

  @override
  void dispose() {
    _heightController.dispose();
    _weightController.dispose();
    _emergencyNameController.dispose();
    _emergencyPhoneController.dispose();
    super.dispose();
  }

  Future<void> _pickDob() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDob ?? DateTime(1995, 1, 1),
      firstDate: DateTime(1920),
      lastDate: now,
      builder: (context, child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: const ColorScheme.light(
              primary: AppColors.primary,
              onPrimary: AppColors.onPrimary,
              surface: AppColors.surfaceContainerLowest,
              onSurface: AppColors.onSurface,
            ),
          ),
          child: child!,
        );
      },
    );

    if (picked != null) {
      setState(() {
        _selectedDob = picked;
      });
    }
  }

  Future<void> _handleSaveProfile() async {
    if (_selectedDob == null) {
      AppSnackbar.showError(context, 'Please select your Date of Birth.');
      return;
    }

    if (!_formKey.currentState!.validate()) return;

    final profile = HealthProfileModel(
      dateOfBirth: _selectedDob!,
      gender: _selectedGender,
      bloodType: _selectedBloodType,
      heightCm: double.tryParse(_heightController.text.trim()),
      weightKg: double.tryParse(_weightController.text.trim()),
      emergencyContactName: _emergencyNameController.text.trim(),
      emergencyContactPhone: _emergencyPhoneController.text.trim(),
    );

    final authController = ref.read(authControllerProvider.notifier);
    final success = await authController.createHealthProfile(profile: profile);

    if (!mounted) return;

    if (success) {
      ref.read(patientProfileProvider.notifier).updateProfile(
            dateOfBirth: _selectedDob,
            gender: _selectedGender,
            bloodType: _selectedBloodType,
            heightCm: _heightController.text.trim(),
            weightKg: _weightController.text.trim(),
            emergencyContactName: _emergencyNameController.text.trim(),
            emergencyContactPhone: _emergencyPhoneController.text.trim(),
          );
      AppSnackbar.showSuccess(context, 'Health profile completed successfully!');
      context.go(AppRoutes.home);
    } else {
      final errorMsg = ref.read(authControllerProvider).errorMessage ?? 'Failed to save profile.';
      AppSnackbar.showError(context, errorMsg);
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authControllerProvider);
    final user = authState.user;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: const AppHeader(
        title: 'Health Profile Setup',
        showBack: false,
      ),
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: AppSpacing.paddingScreenAll,
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 540),
              child: Column(
                children: [
                  AppCard(
                    padding: EdgeInsets.zero,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // 1. Header Banner
                        Container(
                          padding: AppSpacing.paddingCard,
                          decoration: const BoxDecoration(
                            color: AppColors.surfaceContainerLow,
                            borderRadius: AppRadius.radiusTopLg,
                            border: Border(
                              bottom: BorderSide(color: AppColors.outlineVariant, width: 1),
                            ),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  AppAvatar(
                                    name: user?.name ?? 'Patient',
                                    imageUrl: user?.avatarUrl,
                                    size: 48,
                                  ),
                                  AppSpacing.gapHMd,
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          user?.name.isNotEmpty == true ? user!.name : 'Welcome Patient',
                                          style: AppTypography.titleMedium.copyWith(
                                            color: AppColors.onSurface,
                                            fontWeight: FontWeight.w700,
                                          ),
                                        ),
                                        Text(
                                          user?.email ?? 'Account Connected',
                                          style: AppTypography.bodySm.copyWith(
                                            color: AppColors.onSurfaceVariant,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  const AppBadge(
                                    text: 'Step 2 of 2',
                                    variant: BadgeVariant.primary,
                                  ),
                                ],
                              ),
                              AppSpacing.gapVMd,
                              Container(
                                padding: const EdgeInsets.all(AppSpacing.sm),
                                decoration: BoxDecoration(
                                  color: AppColors.primaryContainer.withValues(alpha: 0.3),
                                  borderRadius: AppRadius.radiusSm,
                                ),
                                child: Row(
                                  children: [
                                    const Icon(Icons.info_outline_rounded, size: 16, color: AppColors.primary),
                                    AppSpacing.gapHSm,
                                    Expanded(
                                      child: Text(
                                        'Please complete your vital health details to activate your clinical EHR and booking privileges.',
                                        style: AppTypography.labelSm.copyWith(
                                          color: AppColors.primary,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),

                        // 2. Form Fields
                        Padding(
                          padding: AppSpacing.paddingCard,
                          child: Form(
                            key: _formKey,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Date of Birth & Gender
                                Row(
                                  children: [
                                    // DOB
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'DATE OF BIRTH',
                                            style: AppTypography.labelMd.copyWith(
                                              color: AppColors.onSurfaceVariant,
                                              letterSpacing: 0.8,
                                            ),
                                          ),
                                          AppSpacing.gapVSm,
                                          InkWell(
                                            onTap: _pickDob,
                                            borderRadius: AppRadius.radiusBase,
                                            child: Container(
                                              padding: AppSpacing.paddingInput,
                                              decoration: BoxDecoration(
                                                color: AppColors.surfaceContainerLowest,
                                                borderRadius: AppRadius.radiusBase,
                                                border: Border.all(color: AppColors.outlineVariant),
                                              ),
                                              child: Row(
                                                children: [
                                                  const Icon(Icons.calendar_month_outlined, size: 20, color: AppColors.outline),
                                                  AppSpacing.gapHSm,
                                                  Text(
                                                    DateFormatter.formatDate(_selectedDob),
                                                    style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    AppSpacing.gapHMd,

                                    // Gender
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'GENDER',
                                            style: AppTypography.labelMd.copyWith(
                                              color: AppColors.onSurfaceVariant,
                                              letterSpacing: 0.8,
                                            ),
                                          ),
                                          AppSpacing.gapVSm,
                                          Container(
                                            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                                            decoration: BoxDecoration(
                                              color: AppColors.surfaceContainerLowest,
                                              borderRadius: AppRadius.radiusBase,
                                              border: Border.all(color: AppColors.outlineVariant),
                                            ),
                                            child: DropdownButtonHideUnderline(
                                              child: DropdownButton<String>(
                                                value: _selectedGender,
                                                isExpanded: true,
                                                icon: const Icon(Icons.expand_more_rounded, color: AppColors.outline),
                                                items: const [
                                                  DropdownMenuItem(value: 'female', child: Text('Female')),
                                                  DropdownMenuItem(value: 'male', child: Text('Male')),
                                                  DropdownMenuItem(value: 'other', child: Text('Other')),
                                                ],
                                                onChanged: (val) {
                                                  if (val != null) {
                                                    setState(() {
                                                      _selectedGender = val;
                                                    });
                                                  }
                                                },
                                              ),
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ],
                                ),
                                AppSpacing.gapVLg,

                                // Blood Type
                                Text(
                                  'BLOOD TYPE',
                                  style: AppTypography.labelMd.copyWith(
                                    color: AppColors.onSurfaceVariant,
                                    letterSpacing: 0.8,
                                  ),
                                ),
                                AppSpacing.gapVSm,
                                Wrap(
                                  spacing: AppSpacing.sm,
                                  runSpacing: AppSpacing.sm,
                                  children: _bloodTypes.map((type) {
                                    final isSelected = _selectedBloodType == type;
                                    return ChoiceChip(
                                      label: Text(type),
                                      selected: isSelected,
                                      selectedColor: AppColors.primaryContainer,
                                      backgroundColor: AppColors.surfaceContainerLowest,
                                      labelStyle: AppTypography.labelMd.copyWith(
                                        color: isSelected ? AppColors.primary : AppColors.onSurface,
                                        fontWeight: FontWeight.w600,
                                      ),
                                      shape: const RoundedRectangleBorder(
                                        borderRadius: AppRadius.radiusBase,
                                        side: BorderSide(color: AppColors.outlineVariant),
                                      ),
                                      onSelected: (selected) {
                                        if (selected) {
                                          setState(() {
                                            _selectedBloodType = type;
                                          });
                                        }
                                      },
                                    );
                                  }).toList(),
                                ),
                                AppSpacing.gapVLg,

                                // Height & Weight
                                Row(
                                  children: [
                                    Expanded(
                                      child: AppTextField(
                                        label: 'HEIGHT (CM)',
                                        hintText: '170',
                                        controller: _heightController,
                                        keyboardType: TextInputType.number,
                                        validator: (v) => Validators.validateRequired(v, 'Height'),
                                      ),
                                    ),
                                    AppSpacing.gapHMd,
                                    Expanded(
                                      child: AppTextField(
                                        label: 'WEIGHT (KG)',
                                        hintText: '65',
                                        controller: _weightController,
                                        keyboardType: TextInputType.number,
                                        validator: (v) => Validators.validateRequired(v, 'Weight'),
                                      ),
                                    ),
                                  ],
                                ),
                                AppSpacing.gapVLg,

                                // Emergency Contact
                                AppTextField(
                                  label: 'EMERGENCY CONTACT NAME',
                                  hintText: 'David Jenkins (Spouse)',
                                  controller: _emergencyNameController,
                                  validator: (v) => Validators.validateRequired(v, 'Emergency contact name'),
                                  prefixIcon: const Icon(Icons.person_outline_rounded, size: 20, color: AppColors.outline),
                                ),
                                AppSpacing.gapVMd,
                                AppTextField(
                                  label: 'EMERGENCY CONTACT PHONE',
                                  hintText: '+1 555-019-9876',
                                  controller: _emergencyPhoneController,
                                  keyboardType: TextInputType.phone,
                                  validator: Validators.validatePhone,
                                  prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.outline),
                                ),
                                AppSpacing.gapVXl,

                                // Submit
                                AppButton(
                                  text: 'Save & Continue to Home',
                                  isLoading: authState.isLoading,
                                  onPressed: _handleSaveProfile,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
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
