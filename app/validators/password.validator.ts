import { FormGroup, AbstractControl } from '@angular/forms';

export function Comp(Name: string, MatchName: string) {
  return (formGroup: FormGroup) => {
    const control = formGroup.controls[Name];
    const matchcontrol = formGroup.controls[MatchName];

    if (matchcontrol.errors && !matchcontrol.errors.mustMatch) {
      return;
    }

    if (control.value !== matchcontrol.value) {
      matchcontrol.setErrors({ mustMatch: true });
    } else {
      matchcontrol.setErrors(null);
    }
  };
}
