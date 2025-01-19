export function unshiftEquipment(modalEquipments, form) {
    modalEquipments.unshift({
        equipment_name: form.equipment_name,
    });
}
export function unshiftMedsup(modalMsupply, form) {
    modalMsupply.unshift({
        med_sup_name: form.med_sup_name,
    });
}
export function unshiftMed(modalBrand, form) {
    modalBrand.unshift({
        brand: form.brand,
    });
}


