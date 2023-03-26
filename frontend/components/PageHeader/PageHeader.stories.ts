import { action } from '@storybook/addon-actions'
import { Meta, Story } from "@storybook/vue3";
import { EVENT_TYPES } from '~/config/constants';
import PageHeader from '~/components/PageHeader/PageHeader.vue';

export default {
  title: "Pages Components/PageHeader",
  component: PageHeader,
  argTypes: {
    eventType: {
      control: { type: 'select' },
      options: Object.values(EVENT_TYPES),
      mapping: EVENT_TYPES
    },
  }
} as Meta<typeof PageHeader>;

const Template: Story = (args) => ({
  components: { PageHeader },
  methods: {
    action
  },
  setup() {
    return {
      args,
    };
  },
  template: `
    <page-header
      v-bind="args"
      @delete="(a) => action('Delete event')(a)"
    >Page title</page-header>
`,
});

export const Default = Template.bind({});
Default.args = {
  buttonTitle: "Delete event",
};
