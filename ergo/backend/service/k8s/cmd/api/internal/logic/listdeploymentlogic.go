package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"log"
	"time"

	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
)

type ListDeploymentLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewListDeploymentLogic(ctx context.Context, svcCtx *svc.ServiceContext) ListDeploymentLogic {
	return ListDeploymentLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *ListDeploymentLogic) ListDeployment(req types.Request) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	// kubectl api-resources
	list, err := clientSet.CoreV1().Nodes().List(context.TODO(), metav1.ListOptions{})
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("node=======")
	fmt.Println("NAME\tSTATUS\tROLES\tAGE\tVERSION\t")
	for _, node := range list.Items {
		fmt.Printf("%s\t [%s]\t %s\t %d\t %v\t %s\t %s\t %v\t %s\t\n",
			node.Name,

			node.Status.Phase,

			node.Status.NodeInfo.OSImage,

			getAge(node.CreationTimestamp.Time),
			node.Status.NodeInfo.KubeletVersion,
			node.Status.Addresses[0].Address,

			node.Status.NodeInfo.OSImage,
			node.Status.NodeInfo.KernelVersion,
			node.Status.NodeInfo.ContainerRuntimeVersion,
		)
	}

	namespaceList, err := clientSet.CoreV1().Namespaces().List(context.TODO(), metav1.ListOptions{})
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("namespace=======")
	fmt.Printf("NAME\tSTATUS\tAGE\n")
	for _, namespace := range namespaceList.Items {
		fmt.Println(namespace.Name, namespace.Status.Phase, getAge(namespace.CreationTimestamp.Time))
	}

	fmt.Println("service=======")
	serviceList, _ := clientSet.CoreV1().Services("").List(context.TODO(), metav1.ListOptions{})
	for _, service := range serviceList.Items {
		fmt.Printf("%s\t%s\t%s\t%s\t%s\t%v\t%v\t%s\n",
			service.Namespace,
			service.Name,
			service.Spec.Type,
			service.Spec.ClusterIP,
			service.Spec.ExternalIPs,
			service.Spec.Ports,
			getAge(service.CreationTimestamp.Time),
			service.Spec.Selector,
		)
	}
	fmt.Println("deployments=======")
	deploymentList, _ := clientSet.AppsV1().Deployments(req.NameSpace).List(context.TODO(), metav1.ListOptions{})
	for _, deployment := range deploymentList.Items {
		//marshal, _ := json.Marshal(deployment)
		//fmt.Println(string(marshal))
		fmt.Printf("%s\t%s\t%d/%d\t%d\t%d\t%d\t%v\n",
			deployment.Namespace,
			deployment.Name,
			deployment.Status.Replicas,
			deployment.Status.ReadyReplicas,
			deployment.Status.UpdatedReplicas,
			deployment.Status.AvailableReplicas,
			getAge(deployment.CreationTimestamp.Time),
			deployment.Spec.Selector.MatchLabels,
		)
	}

	fmt.Println()
	// ingress
	//clientSet.ExtensionsV1beta1().Ingresses()

	return nil, errorx.NewCodeError(200, "查询成功", deploymentList.Items)
}
func getAge(t time.Time) int64 {
	return int64(time.Since(t).Seconds() / 86400)
}
