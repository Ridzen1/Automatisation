import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import InputField from '../InputField.vue'

describe('InputField.vue', () => {
  it('met à jour le texte affiché quand on écrit', async () => {
    const wrapper = mount(InputField)
    const input = wrapper.find('input')

    await input.setValue('Vitest 2026')
    
    expect(wrapper.find('span').text()).toBe('Vitest 2026')
  })
})